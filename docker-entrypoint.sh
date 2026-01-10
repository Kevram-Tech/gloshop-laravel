#!/bin/bash
set -e

# Si .env n'existe pas et que docker.env existe, créer un lien ou copier
if [ ! -f .env ] && [ -f docker.env ]; then
    cp docker.env .env
fi

# Exécuter la commande passée en paramètre
exec "$@"


