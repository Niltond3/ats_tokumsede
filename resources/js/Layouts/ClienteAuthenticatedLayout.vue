<script setup>
import { ref } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import ResponsiveNavLink from '@/components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { StringUtil } from '@/util';
import renderToast from '@/components/renderPromiseToast';
import { logout } from '@/services/api/clientAuth';

const page = usePage();

const name = StringUtil.utf8Decode(page.props.auth.user.nome);

const handleLogout = () => {
  renderToast(
    logout(),
    'Desconectando cliente...',
    'Cliente desconectado com sucesso!',
    'Erro ao desconectar cliente!',
    'error',
    () => {
      location.reload();
    },
    (err) => {
      if (err === 'Network Error') location.reload();
    },
  );
};

const showingNavigationDropdown = ref(false);
</script>

<template>
  <div>
    <Toaster richColors />
    <div>
      <div class="min-h-screen bg-white dark:bg-gray-900">
        <nav class="bg-info dark:bg-gray-800 dark:border-gray-700">
          <!-- Primary Navigation Menu -->
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
              <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                  <Link :href="route('cliente.dashboard')">
                    <ApplicationLogo
                      class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                    />
                  </Link>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                  <!-- <NavLink :href="route('cliente.dashboard')"
                                    :active="route().current('cliente.dashboard')">
                                    Cliente Dashboard
                                </NavLink> -->
                </div>
              </div>

              <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                  <Dropdown align="right" width="48">
                    <template #trigger>
                      <span class="inline-flex rounded-md">
                        <button
                          type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                        >
                          {{ name }}

                          <svg
                            class="ms-2 -me-0.5 h-4 w-4"
                            xmlns="https://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </button>
                      </span>
                    </template>

                    <template #content>
                      <DropdownLink :href="route('cliente.profile.edit')"> Perfil </DropdownLink>
                      <button
                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
                        @click="handleLogout"
                      >
                        Log Out
                      </button>
                    </template>
                  </Dropdown>
                </div>
              </div>

              <!-- Hamburger -->
              <div class="-me-2 flex items-center sm:hidden">
                <button
                  class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                  @click="showingNavigationDropdown = !showingNavigationDropdown"
                >
                  <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path
                      :class="{
                        hidden: showingNavigationDropdown,
                        'inline-flex': !showingNavigationDropdown,
                      }"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"
                    />
                    <path
                      :class="{
                        hidden: !showingNavigationDropdown,
                        'inline-flex': showingNavigationDropdown,
                      }"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Responsive Navigation Menu -->
          <div
            :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
            class="sm:hidden"
          >
            <div class="pt-2 pb-3 space-y-1">
              <!-- <ResponsiveNavLink :href="route('cliente.dashboard')"
                            :active="route().current('cliente.dashboard')">
                            Cliente Dashboard
                        </ResponsiveNavLink> -->
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
              <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                  {{ $page.props.auth.user.name }}
                </div>
                <div class="font-medium text-sm text-gray-500">
                  {{ $page.props.auth.user.email }}
                </div>
              </div>

              <div class="mt-3 space-y-1">
                <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('cliente.logout')" method="post" as="button">
                  Log Out
                </ResponsiveNavLink>
              </div>
            </div>
          </div>
        </nav>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="bg-white dark:bg-gray-800">
          <div
            class="relative max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col items-center gap-2 bg-info text-white *:font-extralight before:bg-bg-cut before:absolute before:h-4 before:w-full before:z-10 before:bottom-0"
          >
            <slot name="header" />
          </div>
        </header>

        <!-- Page Content -->
        <main>
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>
