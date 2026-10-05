<?php
namespace App\Providers;
use App\Models\Book;
use App\Models\JoinCard;
use App\Models\MediaPartner;
use App\Models\Publication;
use App\Models\SiteSetting;
use App\Models\User;
use App\Policies\BookPolicy;
use App\Policies\JoinCardPolicy;
use App\Policies\MediaPartnerPolicy;
use App\Policies\PublicationPolicy;
use App\Policies\SiteSettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider {
    public function register(): void {}
    public function boot(): void
    {
        // Public Render traffic always uses HTTPS after proxy termination.
        // Ensure generated route/form URLs never downgrade credentials to HTTP.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Publication::class, PublicationPolicy::class);
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(MediaPartner::class, MediaPartnerPolicy::class);
        Gate::policy(SiteSetting::class, SiteSettingPolicy::class);
        Gate::policy(JoinCard::class, JoinCardPolicy::class);
    }
}
