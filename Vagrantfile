# -*- mode: ruby -*-
# vi: set ft=ruby :

PROJECT_NAME = "sphere-neos-demo"
IP_ADDRESS = "10.1.0.20"

Vagrant.configure(2) do |config|
  config.vm.hostname = PROJECT_NAME + ".vm"

  config.vm.box = "ubuntu/trusty64"

  config.vm.network "private_network", ip: IP_ADDRESS

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "2048"]
  end

  config.vm.hostname = PROJECT_NAME + ".vm"
    config.hostsupdater.aliases = [
      PROJECT_NAME + ".vmdev"
    ]

  # disable default shared folder
  config.vm.synced_folder ".", "/vagrant", disabled: true

  # share site folder into releases folder
  config.vm.synced_folder ".", "/var/www/sphere-neos-demo",
    type: "rsync",
    group: "web",
    rsync__args: ["--verbose", "--archive", "--delete", "-z", "--perms", "--chmod=Dg+s,Dg+rwx"],
    rsync__exclude: [
      ".git/",
      ".idea/",
      ".vagrant/",
      ".DS_Store",
      "Configuration/PackageStates.php",
      "Configuration/Production/" + PROJECT_NAME.gsub(".", "").capitalize + "vm",
      "Configuration/Development/" + PROJECT_NAME.gsub(".", "").capitalize + "vm",
      "Configuration/Testing/Behat",
      "Data/Sessions/**",
      "Data/Temporary/**",
      "Data/Persistent/**",
      "Data/Surf/**",
      "Data/Logs/**",
      "Web/_Resources/Persistent",
      "Web/_Resources/Static"
    ]
end
