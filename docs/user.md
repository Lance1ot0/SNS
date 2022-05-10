# How to use the `User` class ?

First, you have to instanciate the class.
```php
$user = new User();
```

## Methods

Get all the users :
```php
$user->get_all(): Array;
```

Get a single user :
```php
$user->get_single($id: integer): Object;
```

Create a user :
```php
$user->create(
  $firstname: string, 
  $lastname: string,
  $email: string,
  $password: string, 
  $profile_picture: string,
  $banner: string,
  $is_active: boolean
);
```

Update a user :
```php
$user->update(
  $id: integer, 
  $new_user_data: Array(
    $firstname: string, 
    $lastname: string, 
    $email: string, 
    $password: string, 
    $profile_picture: string, 
    $banner: string, 
    $is_active: boolean
  )
)
```

Delete a user :
```php
$user->delete($id: integer);
```

Log in a user :
```php
$user->login($email: string, $password: string);
```

Log out a user :
```php
$user->logout()
```

Follow a user :
```php
$user->follow($id: integer, $following_id: integer);
```

Unfollow a user :
```php
$user->unfollow($id: integer, $following_id: integer);
```

Get user's followings:
```php
$user->get_followings($id: integer): Array;
```

Get user's followers :
```php
$user->get_followers($id: integer): Array;
```

Check if user is following another user :
```php
$user->is_following($id: integer, $following_id: integer): boolean;
```

Check if a user exists :
```php
$user->is_existing($id: integer): Array($user_exists: boolean, $stmt: Object);
```