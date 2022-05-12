# How to use the `Post` class ?

First, you have to instanciate the class.

```php
$post = new Post();
```

## Methods

Get all posts :

```php
$post->get_all(): Array($post: Array, $author: Array);
```

Get a single post :

```php
$post->get_single(): Array;
```

Get all posts from a user :

```php
$post->get_all_from_user($id: integer): Array($posts: Array, $author: Array);
```

Create a post :

```php
$post->create($id: integer, $content: string);
```

Check if a post exists :

```php
$post->is_existing($id: integer): boolean;
```
