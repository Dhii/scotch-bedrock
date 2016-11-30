# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    ### change these two parameters unique to your project!
    #================================================================

    hostname = "scotchbox"
    ip = "192.168.33.10"

    #================================================================

    name_realhost = "#{hostname}.dev"
    name_conf = "#{name_realhost}.conf"
    name_default_conf = "000-default.conf"
    name_docroot = "web"

    # Apache vhosts root path (absolute)
    path_apache_www = "/var/www"
    # Directory with Apache configs
    path_apache_conf_root = "/etc/apache2/sites-available"
    # Where project is stored - must be under Apache vhosts root
    path_project_root = "#{path_apache_www}"
    # Path to docroot - relative to project root
    path_rel_docroot = "#{name_docroot}"
    # Path (absolute) to composer cache dir in guest
    # Must be within apache vhosts root to be effective between guest creations
    path_composer_cache = "#{path_project_root}/.composer-cache"
    # Temporary path (absolute) to clone components into during installation
    path_tmp_clone = "#{path_project_root}/tmp"
    # Path to the WP CLI executable
    path_wp_cli = "/usr/local/bin/wp"

    url_wpcli = "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar"
    url_bedrock = "https://github.com/roots/bedrock.git"
    url_wordpress = "http://#{name_realhost}"

    # WP Provisioner dependency Composer package name
    dep_wp_provisioner = "dnaber/wp-provisioner"

    db_name = "scotchbox"
    db_user = "root"
    db_password = "root"
    db_host = "127.0.0.1"

    wp_admin_user = "admin"
    wp_admin_password = "admin"
    wp_admin_email = "#{wp_admin_user}@#{name_realhost}"

    # Path to docroot - absolute - do not edit
    path_abs_docroot = "#{path_project_root}/#{path_rel_docroot}"
    # Path to project Apache config
    path_apache_conf = "#{path_apache_conf_root}/#{name_conf}"

    config.vm.network "private_network", ip: "#{ip}"
    config.vm.hostname = hostname
    config.vm.box = "scotch/box"
    config.hostsupdater.aliases = ["www.#{name_realhost}","#{name_realhost}"]
    config.vm.synced_folder ".", "#{path_apache_www}", :mount_options => ["dmode=777", "fmode=666"]

    # Optional NFS. Make sure to remove other synced_folder line too
    #config.vm.synced_folder ".", "#{path_apache_www}", :nfs => { :mount_options => ["dmode=777","fmode=666"] }

    # to get WordPress up and running
    config.vm.provision "shell" do |s|
        s.privileged = false
        s.inline = <<-SHELL
            echo "Updating Composer"
            sudo composer self-update

            echo -n 'Checking for WP CLI... '
            if [ ! -f "#{path_wp_cli}" ]; then
                echo "Downloading and installing WP CLI"
                cd "/tmp" && sudo wget -q "#{url_wpcli}" && sudo chmod +x "/tmp/wp-cli.phar" && sudo mv "/tmp/wp-cli.phar" "#{path_wp_cli}"
            else
                echo ' found! Updating'
                sudo #{path_wp_cli} cli update --allow-root --yes
            fi

            echo -n 'Checking for Bedrock... '
            if [ ! -d "#{path_abs_docroot}/wp" ] && [ ! -d "#{path_abs_docroot}/app" ]; then
                echo "not found! Downloading and installing Bedrock"
                git clone "#{url_bedrock}" "#{path_tmp_clone}"
                rm -Rf "#{path_tmp_clone}/.git"
                shopt -s dotglob
                mv "#{path_tmp_clone}"/* "#{path_project_root}"
                rmdir "#{path_tmp_clone}"
            else
                echo ' found! Skipping'
            fi

            echo "Downloading dependencies"
            export COMPOSER_CACHE_DIR="#{path_composer_cache}"
            cd "#{path_project_root}"
            composer config minimum-stability dev
            composer install --prefer-dist && composer require #{dep_wp_provisioner}

            echo -n 'Checking for virtual host... '
            if [ ! -f "#{path_apache_conf}"  ]; then
                echo 'Not found! Creating new virtual host with config based on default:'
                echo "#{path_apache_conf}"
                sudo cp "#{path_apache_conf_root}/#{name_default_conf}" "#{path_apache_conf}"
                sudo sed -i "s!public!#{path_rel_docroot}!g" "#{path_apache_conf}"
                sudo sed -i "s!#ServerName www.example.com!ServerName #{name_realhost}!g" "#{path_apache_conf}"
            else
                echo ' found! Skipping'
            fi

            echo "Activating new virtual host"
            sudo a2ensite #{name_realhost}
            sudo service apache2 reload

            echo -n 'Checking for WordPress... '
            if [ ! -f "#{path_project_root}/.env" ]; then
                echo 'not found! Installing'
                echo "Writing WordPress config"
                printf "DB_NAME=#{db_name}\nDB_USER=#{db_user}\nDB_PASSWORD=#{db_password}\nDB_HOST=#{db_host}\nWP_ENV=development\nWP_HOME=http://#{name_realhost}\nWP_SITEURL=http://#{name_realhost}/wp" > "#{path_project_root}/.env"
                echo "Installing WordPress"
                /home/vagrant/.rbenv/shims/mailcatcher --http-ip=0.0.0.0
                cd "#{path_abs_docroot}" && wp core install --allow-root --url=#{url_wordpress} --title=#{hostname} --admin_user=#{wp_admin_user} --admin_password=#{wp_admin_password} --admin_email=#{wp_admin_email} --quiet
                echo 'WordPress installation finished!'
            else
                echo ' found! Skipping'
            fi

            echo "Cleaning up repo"

            echo "Complete!"
            echo "Go to #{url_wordpress} in your browser"
        SHELL
    end

    config.vm.provider :virtualbox do |vb|
		    vb.gui = false
		    vb.memory = 2048
        vb.name = hostname
        vb.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
        vb.customize ['modifyvm', :id, '--natdnsproxy1', 'on']
    end

end
