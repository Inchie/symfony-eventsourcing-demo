## Eventsourcing with Symfony & Flow

This example demonstrates EventSourcing with symfony & flow.

### Installation with docker

```
cd .docker
```

```
docker-compose up -d
```

Execute into the event-sourcing.app container

```
docker exec -it event-sourcing.app bash
```

#### Inside the container

```
composer install
```

Create the event store(s)

```
php bin/console eventsourcing:store-setup
```

Migrate the domain models (for this example)

```
php bin/console doctrine:migrations:migrate
```

#### Browser

Open your browser and insert localhost

#### Trouble

Perhaps you have to change the file permissions