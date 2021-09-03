# flarum-sonic
Support search by [Sonic](https://github.com/valeriansaliou/sonic)

1. Install Sonic following [this guide](https://github.com/valeriansaliou/sonic#installation)

2. Install the extension:

```
composer require ganuonglachanh/sonic
```

3. Change info in admin setting

4. Then create first index by this command (only run once, new posts will auto index):

```
php flarum sonic:addtoindex
```
