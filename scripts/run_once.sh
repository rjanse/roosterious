#!/bin/sh
BASEDIR=$(dirname $0)
cd $BASEDIR
php run_once.php $1 $2
