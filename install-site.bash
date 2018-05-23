#!/bin/bash
set -e
set -u

q=quirky

if [ $# -ne 1 ]
then
  echo >&2 "Usage: docker-compose exec $q $0 \$(docker-compose port $q 80)"
  exit 1
fi

drush --yes si \
    standard \
    --locale=en-US \
    --db-url=mysql://$q:$q@db/$q \
    --account-name=admin \
    --account-pass=admin \
    --account-mail=$q@$q.invalid \
    install_configure_form.update_status_module='array(FALSE,FALSE)'

drush --yes en php

drush --uri=http://$1 uli
