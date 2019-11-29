# AddChat Codeigniter Lite

Welcome to AddChat Codeigniter Lite.

### All-in-one multi-purpose Chat Widget Codeigniter Pacakge

AddChat is a new chatting friend of Codeigniter. It's a standalone Chat widget that uses the website's existing `users` base, and let website users chat with each other. 

<br>

You get full source-code, hence AddChat lives and runs on your server/hosting including database. And therefore, you get complete privacy over your data. Either you're a big corporate sector or a small business. AddChat is for everyone.

---

#### Read the documentation live - [AddChat Codeigniter Lite Docs](https://addchat-docs.classiebit.com)

#### Live Preview - [AddChat Codeigniter Lite](https://addchat-codeigniter.classiebit.com)

---

![AddChat Lite - Codeigniter Chat Widget](https://addchat-docs.classiebit.com/images/addchat-docs-banner-1.jpg "AddChat Lite - Codeigniter Chat Widget")

---

> **Here's a complete video tutorial guide for getting started quickly [AddChat Codeigniter Academy](https://classiebit.com/academy/addchat-codeigniter/getting-started) ✌️**

---

## Overview

**Addchat Lite** is a chat widget that you can integrate into an existing or a fresh Codeigniter website. AddChat works like a standalone widget and fulfills all your business-related needs like -

1. User-to-user chatting
2. Live real-time chatting (without page refresh)
3. **Internal** notification system (saves **Pusher** monthly subscription fees)
4. Customer support (<larecipe-badge type="black" circle icon="fa fa-lock"></larecipe-badge> Pro)
5. Multi-user groups (<larecipe-badge type="black" circle icon="fa fa-lock"></larecipe-badge> Pro)

and a lot more features available in in **AddChat Codeigniter Pro** ⚡️


## Why AddChat ?

Some of the key highlights, why you would like to go with AddChat!

- Save monthly subscription bills (pay once use forever)
- No Confidential Data leak
- Complete Privacy
- Easy to install & update
- Use existing users database
- Multi-purpose, use it as Helpdesk, Customer support, User-to-user chatting and much more...

---

> **AddChat never modifies your existing database tables or records. And it never breaks down any of your website functionality.**

---

> **AddChat is fully tested and ready to be used in production websites.**

---


## Technical Specification

AddChat is very light, high performance, scalable and secure.

1. AddChat front-end built with **VueJs**, which is purely API based web-app.

2. AddChat back-end (API) built with **Codeigniter**

    - **AddChat Codeigniter** version comes with an installer, which auto-install AddChat in an existing or a fresh Codeigniter website



## User Interface & Design

AddChat is designed in **CSS Flexbox** and **Sass**. Let's see what's so special about **CSS Flexbox** and why we used it.

1. AddChat is a CSS Framework Independent. Means, no matter in which CSS Framework your website is in, it neither affects the website CSS nor gets affected by it.

    - [Bootstrap](https://getbootstrap.com/) 
    - [Bulma](https://bulma.io/) 
    - [Materializecss](https://materializecss.com/) 
    - [Semantic UI](https://semantic-ui.com/) 
    - [UIKit](https://getuikit.com/) 
    - [Zurb Foundation](https://foundation.zurb.com/) 

    or any other...

2. AddChat CSS is completely encapsulated (wrapped in AddChat wrapper with `#addchat-bot .c-` prefix).
    - Hence, it never override your website CSS nor inherits from it.

    - AddChat UI is extra-responsive. Optimized for **extra-small** devices to large **4K desktops** -

        * Small phones
        * Android Phones
        * iPhones
        * iPad & iPad Pro0
        * Small-Medium Size Laptops
        * Large Desktops

3. We've used the popular **NPM** package `auto-prefixer` to make the AddChat UI design same across all types of browsers e.g `Chrome, Firefox, Safari, Edge` etc



## Multi-regional

AddChat is compatible with all languages and timezones. AddChat auto adapts and adjust website's default timezone and language. Please refer to the Language section for more info about **adding a new language** in [AddChat Codeigniter](https://addchat-docs.classiebit.com/docs/1.0/admin/multi-language-codeigniter)

--- 

> **AddChat never breaks any of your website functionality, even if something went wrong with AddChat, there are `fallback modes` for every worst-case scenario.**

---


## Lite Version

---

> **This is AddChat Lite version documentation**

---


**AddChat Lite** is open-source, free to use. Lite version has got limited features & functionality.

- **AddChat Codeigniter Lite**

    + [Live](https://addchat-codeigniter.classiebit.com) - Visit live.
    + [Github](https://github.com/classiebit/addchat-codeigniter) - Give us a Star.
    + [Download](https://classiebit.com/addchat-codeigniter) - Visit here to download


## Pro Version

**AddChat Pro Version** comes with **Commercial** license. Pro version is fully loaded with a lot of useful and exciting features.

- **AddChat CodeIgniter Pro**

    + [Live](https://addchat-codeigniter-pro.classiebit.com) - Live preview available now.
    + [Purchase](https://classiebit.com/addchat-codeigniter-pro) - Available for purchase now - Flat 50% Off (limited time offer)

---

# Codeigniter Installation

AddChat CodeIgniter comes with an installer that makes the installation process fully automated and smooth 🍻

---

![AddChat CodeIgniter Installer](https://addchat-docs.classiebit.com/images/addchat-codeigniter-lite-installer.jpg "AddChat CodeIgniter Installer")

---

> **Here's a complete video tutorial guide for getting started quickly [AddChat CodeIgniter Academy](https://classiebit.com/academy/addchat-codeigniter/getting-started) ✌️**

---

### Server Requirements

* PHP version **5.6** or newer is recommended.
* Make sure **.htaccess** is enabled.
* CodeIgniter website with an **Authentication** (user-login) system. 


### Remember

* The website directory must have proper **write** permissions e.g `sudo chown -R :www-data yourwebsite`
* Change CSRF regenerate to `FALSE` in `application/config/config.php` `$config['csrf_regenerate'] = false`


### Install

1. Download & Unzip the package.
2. Copy the **addchat_installer** folder and paste it into your website root directory.
3. After doing so, your website directory will look like this.

    ```bash

    yoursite.com
    │
    ├── addchat_installer
    ├── application
    ├── system
    │
    ├── ..
    ├── ..
    ├── ..
    │
    ├── .htaccess
    └── index.php

    ```

4. Visit `yoursite.com/addchat_installer` to run the installer. 


---

> **Make sure **.htaccess** files exist and not hidden.**

---

> **Do not forget to delete the **addchat_installer** folder after successful installation.**

---

## Installer Instructions


#### Database

- Enter your website's existing database credentials.

<br>

#### Assets

- Enter your website assets folder path.

<br>

#### Config

- Enter your website config folder path. e.g `application/config`
- Enter **LOGGED-IN USER-ID SESSION KEY NAME** e.g user_id

    ---

    > **The $_SESSION variable key name in which your application stores the logged-in user id e.g $_SESSION['user_id'] then the key is *user_id***

    ---

<br>

#### Application

- Enter **Controllers** Folder Path e.g `application/controllers`
- Enter **Libraries** Folder Path e.g `application/libraries`
- Enter **English Language** Folder Path e.g `application/language/english`

---

> **And finally click install to start the installation process.**

---


### After successful installation, you need to do one simple step manually.

1. Open the common layout file, mostly the common layout file is the file which contains the HTML & BODY tags.

    - Copy AddChat CSS code and paste it right before closing **&lt;/head&gt;** tag

        ```php
        <!-- 1. Addchat css -->
        <link href="<?php echo base_url('assets/addchat/css/addchat.min.css') ?>" rel="stylesheet">
        ```
    
    - Copy AddChat Widget code and paste it right after opening **&lt;body&gt;** tag

        ```php
        <!-- 2. AddChat widget -->
        <div id="addchat_app" 
            data-baseurl="<?php echo base_url() ?>"
            data-csrfname="<?php echo $this->security->get_csrf_token_name() ?>"
            data-csrftoken="<?php echo $this->security->get_csrf_hash() ?>"
        ></div>
        ```

    - Copy AddChat JS code and paste it right before closing **&lt;/body&gt;** tag

        ```php
        <!-- 3. AddChat JS -->
        <!-- Modern browsers -->
        <script type="module" src="<?php echo base_url('assets/addchat/js/addchat.min.js') ?>"></script>
        <!-- Fallback support for Older browsers -->
        <script nomodule src="<?php echo base_url('assets/addchat/js/addchat-legacy.min.js') ?>"></script>
        ```

    <br>

    #### The final layout will look something like this

    ```php
    <head>

        <!-- **** your site other content **** -->

        <!-- 1. Addchat css -->
        <link href="<?php echo base_url('assets/addchat/css/addchat.min.css') ?>" rel="stylesheet">

    </head>
    <body>

        <!-- 2. AddChat widget -->
        <div id="addchat_app" 
            data-baseurl="<?php echo base_url() ?>"
            data-csrfname="<?php echo $this->security->get_csrf_token_name() ?>"
            data-csrftoken="<?php echo $this->security->get_csrf_hash() ?>"
        ></div>


        
        <!-- **** your site other content **** -->



        <!-- 3. AddChat JS -->
        <!-- Modern browsers -->
        <script type="module" src="<?php echo base_url('assets/addchat/js/addchat.min.js') ?>"></script>
        <!-- Fallback support for Older browsers -->
        <script nomodule src="<?php echo base_url('assets/addchat/js/addchat-legacy.min.js') ?>"></script>

    </body>
    ```

---

> **Replace the `assets` by your website's assets path.**

---

> **`addchat.min.js` for modern browsers & `addchat-legacy.min.js` for older browsers. These will be used switched by the browsers automatically on the basis on `type="module"` & `nomodule`, you need to nothing.**

---

> **Setup finishes here, now heads-up straight to [Settings](https://addchat-docs.classiebit.com/docs/1.0/admin/settings) docs**

---

#### Must read the documentation for getting started - [AddChat Codeigniter Lite Docs](https://addchat-docs.classiebit.com)

---