# faker-image

COMO USAR:

- Em seu `composer.json` adicione o seguinte bloco de código:
```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/adaptiOficial/faker-image"
    }
],
```

- Em seguida, instale o pacote pelo composer:
```bash
composer require adapti/faker-image
```
- Agora é só usar o pacote importando a classe:
```php
use Adapti\FakerImage\FakerImage;
```

Exemplo de utilização:
```php
(new FakerImage)->image('banner')
```
