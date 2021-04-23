# wp-datalayer
Plugin for the old willow, to fetch and publish data to the datalayer

#### Worpress filters available

- __wp_set_commercial_type__
``` php
add_filter('wp_set_commercial_type', function() {
	// tamper commercial type
	return $strCommercialType;
});
```
