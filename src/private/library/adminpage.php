<?php

namespace AS\infra;

require_once(__DIR__ . '/Utils.php');
require_once(__DIR__ . '/admin.php');
include_once(__DIR__ . '/maindb.php');

use AS\library\maindb;
use AS\Utilities\Utils;
use PDO;
use PDOException;

abstract class AdminPage
{
    protected $data = [];
    protected $template = [];
    protected $form = [];
    protected $title = '';
    protected $auth_redirect = '404';
    protected $table_name = '';
    protected $sorts = [];
    protected $sorting_param = '';
    protected $advanced_sorting = '';
    protected $group_by = '';
    protected $filters = [];
    protected $sql_select_query = '';
    protected $sql_where_query = '';
    protected $js_page_info = [];
    protected $is_post = false;
    protected $typed_params = [];
    protected $use_read_only_db_for_select = true;

    protected $params = [
        'id' => ['GET', 'POST'],
        'next_range' => ['GET'],
    ];

    public function __construct() {
        $non_typed_params = [];
        if (!empty($this->params)) {
            $non_typed_params = Utils::read_params($this->params);
        }

        $typed_params = [];
        if (!empty($this->typed_params)) {
            $typed_params = Utils::read_typed_params($this->typed_params);
        }

        $this->form = array_merge($non_typed_params, $typed_params);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->is_post = true;
        }
    }

    public function run() {
        $response = $this->handle_request_base();
        $template_name = $response['template']['twig'];
        start_twig("$template_name.twig", [], $response);
    }

    protected function handle_request() {
    }

    protected function handle_post_delete() {
        if ($this->is_post) {
            $db = maindb::getInstance();
            $query = $db->prepare("DELETE FROM $this->table_name WHERE id = :id");
            $query->execute([':id' => $this->form['id']]);
        }
    }

    abstract protected function set_data();

    abstract protected function set_sorts();

    abstract protected function set_select_query();

    public function handle_request_base() {
        $this->set_template('base');
        $this->set_data();
        $this->handle_post_delete();
        $this->set_sorts();
        $this->set_select_query();
        $this->handle_request();
        $this->init_manage();
        $response = $this->build_response();
        return $response;
    }

    protected function build_response() {
        $this->template['title'] = $this->title;
        $select_result = $this->select_items();
        $this->handle_selected_items($select_result);

        $response = [
            'data' => $this->data,
            'template' => $this->template,
            'form' => $this->form,
            'js_page_info' => $this->js_page_info,
        ];
        return $response;
    }

    final protected function set_template($template_name) {
        $this->template =
        [
            'twig' => "admin/$template_name",
            'tempalte' => $template_name,
        ];
    }

    protected function init_manage() {
        $this->is_auth();
        if (empty($this->form['sort'])) {
            $this->form['sort'] = "ID";
        }
        if (empty($this->form['sort_order']) || $this->form['sort_order'] === "DESC") {
            $this->form['sort_order'] = "DESC";
        } else {
            $this->form['sort_order'] = "ASC";
        }

        if (empty($this->form['next_range'])) {
            $this->form['next_range'] = 0;
        } else {
            $this->form['next_range'] = (int)$this->form['next_range'];
        }
        if (empty($this->form['id'])) {
            $this->form['id'] = '';
        }

        $this->sorting_param = $this->form['sort'];
        $this->form['prev'] = $this->form['next_range'] > 0 ? ($this->form['next_range'] - 50) : null;
        $this->form['sorts_array'] = $this->sorts;
        $this->form['filters'] = $this->filters;
    }

    protected function select_items() {
        if (empty($this->sql_select_query)) {
            return [];
        }
        $db = maindb::getInstance();

        $where_params = [];//$this->filters_result['where_params'];
        $where = '';//  $this->filters_result['where'];
        $curr_filters = '';// =  $this->filters_result['curr_filters'];
        $this->form['curr_filters_string'] = $curr_filters;
        if (!empty($where)) {
            $this->sql_select_query .= " WHERE $where";
        }
        if (!empty($this->sql_where_query)) {
            $this->sql_select_query .=
                " AND $this->sql_where_query";
        }
        if (!empty($this->group_by)) {
            $this->sql_select_query .=
                " GROUP BY $this->group_by";
        }

        if ($this->advanced_sorting == '') {
            $this->sql_select_query .= " ORDER BY $this->sorting_param {$this->form['sort_order']}";
        } else {
            $this->sql_select_query .= " $this->advanced_sorting";
        }

        $this->sql_select_query .= " OFFSET :starting LIMIT :limit";

        $stmt = $db->prepare($this->sql_select_query);
        $stmt->bindParam(':starting', $this->form['next_range'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', 50 + 1, PDO::PARAM_INT);
        foreach ($where_params as $where_param => $whereValue) {
            $stmt->bindValue($where_param, $whereValue);
        }

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getMessage() == "SQLSTATE[HY000]: General error: 3024 Query execution was interrupted, maximum statement execution time exceeded") {
                echo "Sorry, this search is taking too long, please add more filters and try again";
                die();
            } else {
                echo "Something went wrong, please try again later or talk to a dev" . $e->getMessage();
                die();
            }
        }

        return $stmt->fetchAll();
    }

    protected function handle_selected_items($select_result) {
        $row_count = sizeof($select_result);
        for ($i = 0; $i < min(50, $row_count); $i++) {
            $this->data['results'][] = $this->handle_selected_item($select_result[$i]);
        }

        $this->form['next'] = $row_count > 50 ? ($this->form['next_range'] + 50) : null;
    }

    protected function handle_selected_item($selected_item) {
        $curr_item = [];
        foreach ($this->sorts as $column) {
            $row_data = '';
            $column_name_array = explode('.', $column['mysql_column']);
            $column_name = array_pop($column_name_array);
            if (method_exists($this, 'data_for_column_' . $column_name)) {
                $row_data = $this->{'data_for_column_' . $column_name}($selected_item, $column);
            } elseif (isset($selected_item[$column_name])) {
                $row_data = $selected_item[$column_name];
            }

            $curr_item[$column['name']] = $row_data;
        }
        return $curr_item;
    }

    private function is_auth() {
        if (empty($_COOKIE['admin']) || $_COOKIE['admin'] !== 'T70gFw2PXH') {
            $this->redirect($this->auth_redirect);
        }
    }

    protected function redirect($page) {
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host/$page");
        exit;
    }

    protected function set_redirect($auth_redirect) {
        $this->auth_redirect = $auth_redirect;
    }

    protected function get_row_number(): int {
        if (empty($this->data['results'])) {
            return 1;
        } else {
            return count($this->data['results']) + 1;
        }
    }
}
