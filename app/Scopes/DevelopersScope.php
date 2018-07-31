<?php

namespace App\Scopes;

use Adldap\Query\Builder;
use Adldap\Laravel\Scopes\ScopeInterface;

class DevelopersScope implements ScopeInterface
{
    /**
     * Apply the scope to a given LDAP query builder.
     *
     * @param Builder $query
     *
     * @return void
     */
    public function apply(Builder $query)
    {
        // The distinguished name of our LDAP group.
        $basedn = config('adldap.connections.default.connection_settings.base_dn');
        $devs = sprintf("CN=Developers,OU=Security Groups,OU=General Distribution - Security Groups,OU=AllGroups,%s", $basedn);

        $query->whereMemberOf($devs);
    }
}