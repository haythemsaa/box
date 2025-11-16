<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link href="/" class="text-2xl font-bold text-indigo-600">
                                BoxManager
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink href="/dashboard" :active="$page.url === '/dashboard'">
                                Tableau de bord
                            </NavLink>
                            <NavLink href="/sites" :active="$page.url.startsWith('/sites')">
                                Sites
                            </NavLink>
                            <NavLink href="/boxes" :active="$page.url.startsWith('/boxes')">
                                Box
                            </NavLink>
                            <NavLink href="/customers" :active="$page.url.startsWith('/customers')">
                                Clients
                            </NavLink>
                            <NavLink href="/contracts" :active="$page.url.startsWith('/contracts')">
                                Contrats
                            </NavLink>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Tenant Info -->
                        <div class="ml-3 relative" v-if="$page.props.auth.tenant">
                            <div class="flex items-center text-sm text-gray-700">
                                <span class="font-medium">{{ $page.props.auth.tenant.name }}</span>
                                <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $page.props.auth.tenant.plan }}
                                </span>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="ml-3 relative" v-if="$page.props.auth.user">
                            <div class="flex items-center text-sm text-gray-700">
                                <span>{{ $page.props.auth.user.name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <ResponsiveNavLink href="/dashboard" :active="$page.url === '/dashboard'">
                        Tableau de bord
                    </ResponsiveNavLink>
                    <ResponsiveNavLink href="/sites" :active="$page.url.startsWith('/sites')">
                        Sites
                    </ResponsiveNavLink>
                    <ResponsiveNavLink href="/boxes" :active="$page.url.startsWith('/boxes')">
                        Box
                    </ResponsiveNavLink>
                    <ResponsiveNavLink href="/customers" :active="$page.url.startsWith('/customers')">
                        Clients
                    </ResponsiveNavLink>
                    <ResponsiveNavLink href="/contracts" :active="$page.url.startsWith('/contracts')">
                        Contrats
                    </ResponsiveNavLink>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white shadow" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const showingNavigationDropdown = ref(false);
</script>
