# Shopping! With Friends!

**Shopping! With Friends!** is bringing the classic mall experience of shopping with your friends into the digital age. Our platform is designed to help users connect, browse, and shop together – all from the comfort of home. Users can start and join groups for shared shopping experiences, exploring products, sharing recommendations, and building wishlists in real time. Think of it as a virtual mall where every session feels like a fun outing with your friends.

## Key Features:
- **Group Shopping Experiences:** Create or join shopping groups with your friends, tailored around specific interests, styles, or events.
- **Real-time Collaboration:** See what your friends are browsing, voting on, or adding to their carts. It's social shopping, online.
- **Exclusive Deals:** Enjoy unique discounts and offers when shopping as a group.
- **Interactive Shopping:** Engage with friends through themed sessions, wishlist sharing, and instant feedback on products.

---

## 🔒 Security First: Application Security Review

We believe that a safe, secure platform is essential to delivering a stress-free shopping experience. Before expanding **Shopping! With Friends!**, we’re committed to ensuring the platform meets the highest standards of security and privacy.

**Why Security Matters:**
- **User Privacy:** Protecting our users' personal information is our top priority.
- **Data Integrity:** Ensuring data shared between users is secure and encrypted is crucial for group interactions.
- **Safe Transactions:** We're focused on providing a secure shopping environment where every transaction is protected.

---

## 🚀 Future Development:

As we continue to build out the platform, we’re looking forward to implementing more features, improving usability, and enhancing group shopping experiences. However, **security remains our top priority**, and we’re taking every measure to ensure that our users can shop with confidence.

---

## Install and Run the project

1. Make sure you have Docker installed and port 80 is available.
2. extract the zip.
3. `cd` into the project directory. 
4. Run `./local/bootstrap` to set up the environment and config.
5. Next run `./local/run` to start the container. 
6. In a new terminal window run `docker compose exec php-fpm bash` to enter the container. 
7. While in the container run `docker-php-ext-install pgsql`. 
8. Exit the container with `exit` and run `./local/mig` to migrate the database. 
9. Visit `http://localhost` in your browser to view the project.

