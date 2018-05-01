#!/bin/bash
pver="5.5"
cver="5.6"
#cdir="~/projects/laraadmin"
cdir=$(pwd)
eval cd "$cdir"
grep -R "|| LAHelper::laravel_ver() == $pver" * | grep -v "|| LAHelper::laravel_ver() == $cver" | sort --unique | cut -d: -f1 | while read -r line ; do
    path="$cdir/$line"
    echo "Processing $path"
    eval "sed -i -e 's/|| LAHelper::laravel_ver() == $pver/|| LAHelper::laravel_ver() == $pver || LAHelper::laravel_ver() == $cver/g' $path"
done
