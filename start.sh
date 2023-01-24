docker-compose up -d
sleep 10
echo "composer"
docker exec -it phpyii sh -c "php composer.phar install"
echo "migrations"
docker exec -it phpyii sh -c "php yii migrate --interactive=0"
echo "import data"
docker exec -it phpyii sh -c "php yii import"
echo "curl"
curl "http://localhost/api/index?date_from=2022-12-01&date_to=2022-12-20&guest_count=4"