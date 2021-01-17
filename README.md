# Анализ access.log
На выходе программа предоставляет временные интервалы, в которые доля отказов системы превышала
указанную границу, а также уровень доступности в этот интервал времени.

## Использование
`cat tests/_data/access-log-data-one-day | php entry.php -u 90 -t 45 -i 60`

`-u` - минимально допустимый уровень доступности

`-t` - приемлемое время ответа

`-i` - измеряемый интервал (необязательный)

## Использование через docker
`docker build --tag analyze-access-log .`

`cat tests/_data/access-log-data-one-day | docker run -i --rm analyze-access-log -- -u 90 -t 45 -i 60`

## Тесты
`vendor/bin/phpunit tests`