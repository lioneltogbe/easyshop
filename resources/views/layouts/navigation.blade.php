<nav class="navbar" style="">
    <div class="navbar-container">
        <!-- Top Navigation Bar -->
        <div class="navbar-top">
            <!-- Logo & Brand -->
            <div class="navbar-brand">
                <a href="{{ route('dashboard') }}" style="text-decoration: none;">
                    <div class="navbar-logo">eS</div>
                </a>
                <!-- <a href="{{ route('dashboard') }}" style="text-decoration: none;">
                    <h1 class="navbar-title">easyShop</h1>
                </a> -->
            </div>

            <!-- Search Bar -->
            <!-- <div class="navbar-search">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Rechercher un produit, client, commande...">
            </div> -->

            <!-- Actions -->
            <div class="navbar-actions">
                <!-- Notifications -->
                <!-- <button class="navbar-icon-btn" title="Notifications">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(isset($alertesCount) && $alertesCount > 0)
                        <span class="notification-badge"></span>
                    @endif
                </button> -->

                <!-- Settings -->
                <!-- <a href="{{ route('parametres.index') }}" class="navbar-icon-btn" title="Paramètres">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a> -->

                <!-- User Profile -->
                <!-- <div class="navbar-user">
                    <div class="navbar-user-info">
                        <div class="navbar-user-name">{{ Auth::user()->name ?? 'Utilisateur' }}</div>
                        <div class="navbar-user-role">{{ Auth::user()->email ?? ' ' }}</div>
                        <div class="navbar-user-role">{{ Auth::user()->getRoleNames()->first() ?? 'Utilisateur' }}</div>
                    </div>
                    <div class="navbar-user-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Navigation Tabs - FILTRÉE PAR RÔLE -->
        <div class="navbar-tabs">
            <!-- Dashboard (Tous les rôles) -->
            <a href="{{ route('dashboard') }}" class="navbar-tab @if(Route::currentRouteName() == 'dashboard') active @endif">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            @php
                $productSection = strpos(Route::currentRouteName(), 'produits') !== false || strpos(Route::currentRouteName(), 'categories') !== false || strpos(Route::currentRouteName(), 'stocks') !== false;
                $purchaseSection = strpos(Route::currentRouteName(), 'achats') !== false || strpos(Route::currentRouteName(), 'fournisseurs') !== false;
                $salesSection = strpos(Route::currentRouteName(), 'ventes') !== false || strpos(Route::currentRouteName(), 'clients') !== false;
                $clientSection = strpos(Route::currentRouteName(), 'clients') !== false;
                $financeSection = strpos(Route::currentRouteName(), 'finances') !== false || strpos(Route::currentRouteName(), 'alertes') !== false;
                $adminSection = strpos(Route::currentRouteName(), 'users') !== false || strpos(Route::currentRouteName(), 'roles') !== false;
            @endphp

            @if(Auth::user()->hasRole('admin'))
                <details class="navbar-group @if($adminSection) open @endif">
                    <summary class="navbar-tab @if($adminSection) active @endif">
                        <span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Administration
                        </span>
                        <span class="chevron">▾</span>
                    </summary>
                    <div class="navbar-submenu">
                        @can('view-user')
                            <a href="{{ route('users.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'users') !== false) active @endif">
                                <span>Utilisateurs</span>
                            </a>
                        @endcan
                        @can('manage-roles')
                            <a href="{{ route('roles.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'roles') !== false) active @endif">
                                <span>Rôles</span>
                            </a>
                        @endcan
                    </div>
                </details>
            @endif

            

            @canany(['view-product', 'view-stock'])
                <details class="navbar-group" @if($productSection) open @endif>
                    <summary class="navbar-tab @if($productSection) active @endif">
                        <span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Gestion des produits
                        </span>
                        <span class="chevron">▾</span>
                    </summary>
                    <div class="navbar-submenu">
                        @can('view-product')
                            <a href="{{ route('produits.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'produits') !== false) active @endif">
                                <span>Produits</span>
                            </a>
                            <a href="{{ route('categories.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'categories') !== false) active @endif">
                                <span>Catégories</span>
                            </a>
                        @endcan
                        @can('view-stock')
                            <a href="{{ route('stocks.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'stocks') !== false) active @endif">
                                <span>Stocks</span>
                            </a>
                        @endcan
                    </div>
                </details>
            @endcanany

            @canany(['view-achat', 'view-fournisseur'])
                <details class="navbar-group" @if($purchaseSection) open @endif>
                    <summary class="navbar-tab @if($purchaseSection) active @endif">
                        <span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Gestion des achats
                        </span>
                        <span class="chevron">▾</span>
                    </summary>
                    <div class="navbar-submenu">
                        @can('view-achat')
                            <a href="{{ route('achats.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'achats') !== false) active @endif">
                                <span>Achats</span>
                            </a>
                        @endcan
                        @can('view-fournisseur')
                            <a href="{{ route('fournisseurs.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'fournisseurs') !== false) active @endif">
                                <span>Fournisseurs</span>
                            </a>
                        @endcan
                    </div>
                </details>
            @endcanany

            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager') || Auth::user()->hasRole('vendeur') || Auth::user()->hasRole('comptable'))
                <details class="navbar-group" @if($salesSection) open @endif>
                    <summary class="navbar-tab @if($salesSection) active @endif">
                        <span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Gestion des ventes
                        </span>
                        <span class="chevron">▾</span>
                    </summary>
                    <div class="navbar-submenu">
                        @can('view-vente')
                            <a href="{{ route('ventes.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'ventes') !== false) active @endif">
                                <span>Ventes</span>
                            </a>
                        @endcan
                        @can('view-client')
                            <a href="{{ route('clients.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'clients') !== false) active @endif">
                                <span>Clients</span>
                            </a>
                        @endcan
                    </div>
                </details>
            @endif

            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager') || Auth::user()->hasRole('comptable'))
                <details class="navbar-group" @if($financeSection) open @endif>
                    <summary class="navbar-tab @if($financeSection) active @endif">
                        <span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Gestion financière
                        </span>
                        <span class="chevron">▾</span>
                    </summary>
                    <div class="navbar-submenu">
                        <a href="{{ route('finances.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'finances') !== false) active @endif">
                            <span>Finances</span>
                        </a>
                        @if(Auth::user()->hasRole('admin'))
                            <a href="{{ route('alertes.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'alertes') !== false) active @endif">
                                <span>Alertes</span>
                                @if(isset($alertesCount) && $alertesCount > 0)
                                    <span style="background: #ef4444; color: white; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; margin-left: 0.25rem;">{{ $alertesCount }}</span>
                                @endif
                            </a>
                        @endif
                    </div>
                </details>
            @endif

             <a href="{{ route('parametres.index') }}" class="navbar-tab @if(strpos(Route::currentRouteName(), 'parametres') !== false) active @endif">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span>Paramètres</span>
                </a>

            <!-- Logout (Tous les rôles) -->
            <form method="POST" action="{{ route('logout') }}" style="margin-left: auto;">
                @csrf
                <button type="submit" class="navbar-tab logout">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </div>
    <div>
            <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
            </button>
        </div>

   
</nav>
