#!/bin/sh
BASEDIR=$(dirname $0)
cd $BASEDIR
php run_periodically.php daily
