<template>
  <div>
    <div
      class="min-h-screen bg-gray-100 flex flex-col font-sans leading-none text-gray-700 antialiased"
    >
      <div class="h-screen flex flex-col">
        <!-- Header -->
        <div class="bg-black flex justify-between px-6 py-4">
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <inertia-link href="/">
                <logo />
              </inertia-link>
            </div>
          </div>

          <!-- Settings Dropdown -->
          <div class="hidden sm:flex sm:items-center sm:ml-6">
            <div class="ml-3 relative">
              <jet-dropdown align="right" width="48">
                <template #trigger>
                  <button
                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out"
                  >
                    <img
                      class="h-8 w-8 rounded-full object-cover"
                      :src="$page.user.profile_photo_url"
                      :alt="$page.user.name"
                    />
                  </button>
                </template>

                <template #content>
                  <!-- Account Management -->
                  <div class="block px-4 py-2 text-xs text-gray-400">
                    Manage Account
                  </div>

                  <jet-dropdown-link href="/user/profile"
                    >Profile</jet-dropdown-link
                  >

                  <jet-dropdown-link
                    href="/user/api-tokens"
                    v-if="$page.jetstream.hasApiFeatures"
                    >API Tokens</jet-dropdown-link
                  >

                  <div class="border-t border-gray-100"></div>

                  <!-- Authentication -->
                  <form @submit.prevent="logout">
                    <jet-dropdown-link as="button">Logout</jet-dropdown-link>
                  </form>
                </template>
              </jet-dropdown>
            </div>
          </div>

          <!-- Hamburger -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
            >
              <svg
                class="h-6 w-6"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 24 24"
              >
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

        <div class="flex flex-grow overflow-hidden">
          <div class="flex flex-col bg-white w-26 h-full justify-between">
            <!-- Left side NavBar -->
            <ul
              class="flex flex-col mt-2 text-gray-700 dark:text-gray-400 capitalize"
            >
              <!-- Links -->
              <side-menu
                href="/dashboard"
                :active="$page.currentRouteName == 'dashboard'"
                icon_name="products"
                >Products</side-menu
              >
              <!--
                            <side-menu
                                href="/user/profile"
                                :active="$page.currentRouteName == 'users'"
                                icon_name="users"
                                >Users</side-menu
                            >
                            -->
              <!--
                            <side-menu
                                href="/bookings"
                                :active="$page.currentRouteName == 'bookings'"
                                icon_name="bookings"
                                >Bookings</side-menu
                            >
                            -->
              <side-menu
                v-if="status == 3"
                href="/opportunities"
                :active="$page.currentRouteName == 'opportunities'"
                icon_name="bookings"
                >Opportunities</side-menu
              >
              <side-menu
                v-if="status == 3"
                href="/members"
                :active="$page.currentRouteName == 'members'"
                icon_name="users"
                >Members</side-menu
              >

              <side-menu
                v-if="status == 3"
                href="/packages"
                icon_type="fontawesome"
                icon_name="box"
                :active="$page.currentRouteName == 'packages.index'"
                >Package Editor</side-menu
              >

              <side-menu
                v-if="status == 3"
                href="/subcategory"
                icon_type="fontawesome"
                icon_name="box"
                :active="$page.currentRouteName == 'subcategory.index'"
                >Sub Category</side-menu
              >
            </ul>

            <ul
              class="mt-2 flex flex-col text-gray-700 dark:text-gray-400 capitalize"
              style="margin-top: auto"
            >
              <!-- Links -->
              <side-menu
                href="/user/profile"
                :active="$page.currentRouteName == 'profile.show'"
                icon_name="settings"
                >Settings</side-menu
              >

              <!-- Authentication -->
              <side-menu
                href="/user/profile"
                :active="$page.currentRouteName == 'integration'"
                icon_name="integration"
                >Integration</side-menu
              >
            </ul>
          </div>
          <div class="flex-1 p-4 overflow-y-auto" scroll-region>
            <!-- Page Heading -->
            <!-- <header class="bg-white p-4 shadow">
                <slot name="header"></slot>
            </header> -->
            <slot name="header"></slot>

            <flash-messages class="mt-2" />

            <!-- Page Content -->
            <main>
              <slot></slot>
            </main>

            <!-- Modal Portal -->
            <portal-target name="modal" multiple> </portal-target>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Logo from '../Shared/Logo';
import SideMenu from '../Shared/SideMenu';
import Icon from '../Shared/Icon';
import JetDropdown from './../Jetstream/Dropdown';
import JetDropdownLink from './../Jetstream/DropdownLink';
import FlashMessages from '../Shared/FlashMessages';

export default {
  components: {
    Logo,
    SideMenu,
    Icon,
    JetDropdown,
    JetDropdownLink,
    FlashMessages,
  },

  data() {
    return {
      showingNavigationDropdown: false,
    };
  },

  props: {
    status: Number,
  },

  methods: {
    logout() {
      axios.post('/logout').then((response) => {
        window.location = '/';
      });
    },
  },
};
</script>
