<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Disbursement;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseDelivery;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\Storage;
use App\Models\StorageLocation;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\CustomerPolicy;
use App\Policies\DisbursementPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchaseDeliveryPolicy;
use App\Policies\PurchaseInvoicePolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\RolePolicy;
use App\Policies\StorageLocationPolicy;
use App\Policies\StoragePolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Customer::class => CustomerPolicy::class,
        Supplier::class => SupplierPolicy::class,
        Storage::class => StoragePolicy::class,
        StorageLocation::class => StorageLocationPolicy::class,
        Product::class => ProductPolicy::class,
        Payment::class => PaymentPolicy::class,
        PurchaseOrder::class => PurchaseOrderPolicy::class,
        PurchaseDelivery::class => PurchaseDeliveryPolicy::class,
        PurchaseInvoice::class => PurchaseInvoicePolicy::class,
        Disbursement::class => DisbursementPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "admin" role all permissions
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });
    }
}
