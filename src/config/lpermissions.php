<?php 
return [
    /**
     * Model definitions.
     * If you want to use your own model and extend it
     * to package's model. You can define your model here.
     */
    'role'       => 'Leoche\LPermissions\Models\Eloquent\Role',
    'permission' => 'Leoche\LPermissions\Models\Eloquent\Permission',
    
    /**
     * Cache Minutes
     * Set the minutes that roles and permissions will be cached.
     */
    'cacheMinutes' => 0,
];
