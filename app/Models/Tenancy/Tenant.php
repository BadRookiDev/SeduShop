<?php

namespace App\Models\Tenancy;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends SeduTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;
}

