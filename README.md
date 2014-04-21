# My-PHP-MVC-Framework

A simple, custom-built PHP framework utilising the MVC software pattern.


NOTICE: This is a VERY simple framework, built for custom-use and is not intended for others to us in a live environment. 
Feel free to use this code at your own risk.


## Features

- Applications Areas
- Built in routing system, allows unlimited length, zero-configuration routes and variables
- Runtime creation and compiling of JS & CSS bundles
- Custom-built server-side & client-side validation
- Compact configuration file
- Custom-built database access class
- Image manipulation via WideImage (http://wideimage.sourceforge.net/)
- Custom-built basic authorisation system, including roles-based authorisation
- Custom-build pagination system
- Easy redirection helpers
- Custom-built session & flash messages for errors and conformations
- Integrated development debugging via PHPDebugBar (http://phpdebugbar.com/)
- Runtime error reporting via Whoops (http://filp.github.io/whoops/)
- Integrated Twig templating engine (http://twig.sensiolabs.org/) 


## Further information

### Routing

Routing is handled by the framework and allows urls such as:
  - http://{controller}/{action}/{arg-1}/{arg-2}/{arg-n...}
  - http://{area}/{controller}/{action}/{arg-1}/{arg-2}/?arg-3=value

An area is defined in the config file of the application and simply allows you to segment sections of the site from each other in to easy to manage segments - Admin, Reports, Clients, etc

{Controller} & {action} are linked to classes, for example:

```php
Class Home extends Controller {
  public function index($one, $two){
    echo Input::get("arg-3")
  }
}
```

'arg-1' and 'arg-2' querystrings are accessed via the $one & $two variables
The named 'arg-3' querystring is accessed via the Input classes 'get' method and directly printed out.

Note: Controllers must always extend the 'Controller' baseclass. This allows use of database access via a single instance, debugbar, Twig template rendering, model loading and more...


### Validation

Checking if a form is valid is as easy as calling something like

```php
  if(isPostBack()){
     if($validate->passed()){
      echo("Form validated");
     } else {
      echo("Form NOT validated");
     }
  }

```

And setting up the rules is as easy as creating an associative array:

```php
  $validate->rules(array(
        'requiredField' => array('required' => "true"),
        'emailField' => array(
            'required' => 'true',
            'email' => 'true'
        ),
        'numberField' => array(
            'required' => 'true',
            'number' => 'true',
            'minlength' => 2,
            'maxlength' => 4,
            'min' => 1,
            'max' => 20
        ),
        'remoteField' => array('remote' => 'http://localhost:81/home/remote_val'), // 'http://localhost:8000/ajax/valtest.php'
        'numberRangeField' => array('range' => '1,100'),
        'rangeLengthField' => array('rangelength' => '2,6'),
        'dateField' => array('date' => 'true'),
        'passwordField' => array('required' => 'true'),
        'passwordField2' => array('equalto' => 'passwordField'),
        'urlField' => array('required' => 'true', 'url' => 'true'),
        'selectField' => array('required' => 'true'),
        'agree' => array('required' => 'true'),
        'optionsRadios' => array('required' => 'true'),
        'textarea' => array()
    ));

```

And you can even pass in default values, either hard-coded or from a database:

```php
  $validate->defaultValues(array(
        'numberField' => '3',
        'selectField' => 4,
        'optionsRadios' => 'option2',
        'textarea' => 'test<br />A new line'
    ));

```

### Authorisation & authentication

Users can be authenticated on a class level, or per action by adding one of the following codes:

```php
  1. Auth::secure();
  2. Auth::secure(array("Admin"));
  3. Auth::secure(array("Moderator", "Admin"));
```

1. Simply makes sure a user is logged in
2. Makes sure user is logged in and marked as an Admin
3. makes sure is logged in and marked as Admin or Moderator

By default, if a user is not logged in they are re-directed to /Account/login



#### Credits

Open-source code used in this project is linked to in the 'Features' section.

http://www.php-mvc.net/
Although no code was taken from any project on php-mvc.net, it was a great resource to learn how a simple MVC framework works, and I incorporated a lot of the ideas present in that code, updated them to fit my own personal needs and added them to the framework above.
