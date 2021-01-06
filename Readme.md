## Eventsourcing with Symfony & Flow

This example demonstrates EventSourcing with symfony & flow.

### Installation

<details><summary>With Docker</summary>

```
cd .docker
```

Change the path to your local directory

```
vim .env
```

```
docker-compose up -d
```

Execute into the event-sourcing.app container

```
docker exec -it eventsourcing-app bash
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

</details>

<details><summary>With Vagrant / local Web Server</summary>

Navigate to your project directory. Create the event store(s) 
with the following console command:

```
php bin/console eventsourcing:store-setup
```

Migrate the domain models (for this example)

```
php bin/console doctrine:migrations:migrate
```

</details>

### Usage

#### Writing events

In the symfony controller the commands are created and routed to the 
corresponding command handler. For the blogging context the BlogController 
creates the CreateBlog command and routes it to the BloggingCommandHandler. 
In the handleCreateBlog function the event is stored. 

<details><summary>Example event: <i>BlogWasCreated.php</i></summary>

```php
class BlogWasCreated implements DomainEventInterface
{
/**
* @var string
*/
private $name;

    /**
     * @var UserIdentifier
     */
    private $author;

    /**
     * @var string
     */
    private $streamName;

    /**
     * BlogWasCreated constructor.
     * @param string $name
     * @param UserIdentifier $author
     * @param string $streamName
     */
    public function __construct(
        string $name,
        UserIdentifier $author,
        string $streamName
    )
    {
        $this->name = $name;
        $this->author = $author;
        $this->streamName = $streamName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return UserIdentifier
     */
    public function getAuthor(): UserIdentifier
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getStreamName(): string
    {
        return $this->streamName;
    }
}
```
</details>

```php
<?php
$event = new BlogWasCreated(
    $command->getName(),
    $command->getAuthorIdentifier(),
    $streamName
);

$stream = StreamName::fromString($streamName);

$this->eventStore->commit($stream, DomainEvents::withSingleEvent(
    $event
));
```

#### Reading events

The BloggingCommandHandler can also react to a read command.
The function handleStream returns the event stream.

```php
<?php
$streamName = StreamName::fromString('some-stream');
$eventStream = $this->eventStore->load(StreamName::fromString($streamName))
```

#### Reacting to events

In order to react upon new events you'll need an event listener.
In the Flow EventSourcing package the listeners are called with the 
when*" namings. For the event BlogWasCreated the listenerMethodName 
is 'whenBlogWasCreated'. 

```php
$listenerMethodName = 'when' . (new \ReflectionClass($event))->getShortName();
```

<details><summary>Example projector: <i>BlogListProjector.php</i></summary>

```php
<?php
class BlogListProjector implements ProjectorInterface, EventSubscriberInterface
{
    private $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            // NOTE!!! you always have to use "when*" namings, as otherwise, the EventListenerInvoker
            // will not properly call the right methods here.

            // we only use the EventSubscriber from symfony to figure out which listeners should be called.
            BlogWasCreated::class => ['whenBlogWasCreated']
        ];
    }

    public function whenBlogWasCreated(BlogWasCreated $event, RawEvent $rawEvent)
    {
        
    }
}
```
</details>

In our example listener (BlogListProjector) we need the function whenBlogWasCreated(...).
Now this listener can be used by the event invoker of the EventSourcing package.

```php
$this->eventListener->$listenerMethodName($event, $rawEvent);
```

<b>Note:</b> The listener has to implement the `EventSubscriberInterface` 
and `ProjectorInterface`. Otherwise it will not work properly.

#### Replay projections

With the following command you can rebuild all the projections. 

```php
bin/console eventsourcing:projection-replay-demo
```

### Trouble

Perhaps you have to change the file permissions