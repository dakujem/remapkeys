

Implementation note: 
```php
array_merge($carry, $pair); // breaks numeric keys
$pair + $carry; // breaks the order of elements
array_replace($carry, $pair); // works
```      
