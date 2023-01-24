rm ./postgres/.gitignore
docker-compose up -d
sleep 10
echo "migrations"
docker exec -it phpyii sh -c "php yii migrate --interactive=0"
echo "import data"
docker exec -it phpyii sh -c "php yii import"