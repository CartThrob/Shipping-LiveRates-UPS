#!/bin/bash

# the branch
BRANCH="develop"

# allow branch override
while getopts "b:" OPTION; do
    case $OPTION in
    b)
        BRANCH=$OPTARG
        ;;
    esac
done

# get the base directory of the repo
DIR=$(cd "$(dirname "$0")/.."; pwd)

# get the name of the folder
BASENAME=$(basename $DIR)

# check out branch
git checkout $BRANCH || { echo "Could'nt check out $BRANCH branch"; exit 1; }

# get version number from config.php file
VERSION=$(php -r "include '$DIR/system/user/addons/cartthrob_shipping_ups/addon.setup.php'; echo \CARTTHROB_SHIPPING_UPS_VERSION;")

# go to the directory above the repo
cd ..

# temporarily rename the repo directory
mv $BASENAME cartthrob_shipping_ups_$VERSION

ARTIFACTSDIR=cartthrob_shipping_ups-build

mkdir -p $ARTIFACTSDIR

# add version to zip
zip -r $ARTIFACTSDIR/cartthrob_shipping_ups_$VERSION.zip cartthrob_shipping_ups_$VERSION/system -x "*.DS_Store" -x "__MACOSX*" -x "*composer.lock" -x "*composer.json" -x "*.orig" -x "*.git*"

# move the build directory into the project directory
rm -rf cartthrob_shipping_ups_$VERSION/build
mv $ARTIFACTSDIR cartthrob_shipping_ups_$VERSION/build

# rename the repo back to its original name
mv cartthrob_shipping_ups_$VERSION $BASENAME

# change directory back to repo
cd $DIR
