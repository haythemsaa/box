<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link href="/client/dashboard" class="text-2xl font-bold text-indigo-600">
                                BoxManager
                            </Link>
                            <span class="ml-3 px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $t('clientPortal.clientPortal') }}
                            </span>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink href="/client/dashboard" :active="$page.url === '/client/dashboard'">
                                {{ $t('nav.dashboard') }}
                            </NavLink>
                            <NavLink href="/client/contracts" :active="$page.url.startsWith('/client/contracts')">
                                {{ $t('nav.contracts') }}
                            </NavLink>
                            <NavLink href="/client/invoices" :active="$page.url.startsWith('/client/invoices')">
                                {{ $t('clientPortal.invoices') }}
                            </NavLink>
                            <NavLink href="/client/payments" :active="$page.url.startsWith('/client/payments')">
                                {{ $t('clientPortal.payments') }}
                            </NavLink>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-3">
                        <!-- Language Selector -->
                        <LanguageSelector />

                        <!-- User Dropdown -->
                        <div class="relative" v-if="$page.props.auth.user">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                                        <span>{{ $page.props.auth.user.name }}</span>
                                        <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('client.profile')">
                                        {{ $t('nav.profile') }}
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        {{ $t('nav.logout') }}
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
                    <ResponsiveNavLink href="/client/dashboard" :active="$page.url === '/client/dashboard'">
                        {{ $t('nav.dashboard') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink href="/client/contracts" :active="$page.url.startsWith('/client/contracts')">
                        {{ $t('nav.contracts') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink href="/client/invoices" :active="$page.url.startsWith('/client/invoices')">
                        {{ $t('clientPortal.invoices') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink href="/client/payments" :active="$page.url.startsWith('/client/payments')">
                        {{ $t('clientPortal.payments') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink href="/client/profile" :active="$page.url === '/client/profile'">
                        {{ $t('nav.profile') }}
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
        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import LanguageSelector from '@/Components/LanguageSelector.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const showingNavigationDropdown = ref(false);
</script>
