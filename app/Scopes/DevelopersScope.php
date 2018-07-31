<?php

namespace App\Scopes;

use Adldap\Query\Builder;
use Adldap\Laravel\Scopes\ScopeInterface;
use Illuminate\Support\Facades\Log;

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
        $basedn = config('adldap.connections.default.connection_settings.base_dn'); //config('adldap')['connections']['default']['connection_settings']['base_dn']; //env('ADLDAP_BASEDN');
        $devs = sprintf("CN=Developers,OU=Security Groups,OU=General Distribution - Security Groups,OU=AllGroups,%s", $basedn);
        Log::info("devs: " . $devs);
        $query->whereMemberOf($devs);
    }
}