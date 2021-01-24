# drupal-install.sh
#! /bin/bash
[[ -f .env ]] && source .env
if [ $DRUPAL_VERSION ]
then
    echo "Start with Drupal version ${DRUPAL_VERSION}"
else
    echo "Drupal version is not defined. Set the default version to 8.8"
    DRUPAL_VERSION='9.1.3'
fi

sudo rm -rf drupal/web
sudo rm -rf drupal/mysql
sudo rm -rf database
sudo rm -rf vendor
mkdir drupal/web
curl -fSL "https://ftp.drupal.org/files/projects/drupal-${DRUPAL_VERSION}.tar.gz" -o drupal.tar.gz
mv drupal.tar.gz drupal/web
cd drupal/web
tar -zx --strip-components=1 -f drupal.tar.gz
mkdir modules/custom
#cp -r ../../modules/* modules/custom
rm drupal.tar.gz
mkdir database