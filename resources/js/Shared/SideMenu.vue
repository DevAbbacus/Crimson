<template>
  <li :class="classes">
    <inertia-link :href="href" class="flex flex-col items-center">
      <span v-if="icon_type === 'fontawesome'">
        <i v-bind:class="'fas fa-' + icon_name"></i>
      </span>
      <icon v-else class="h-5 w-5" :name="icon_name" />
      <span class="text-sm mt-2">
        <slot></slot>
      </span>
    </inertia-link>
  </li>
</template>

<script>
import Icon from './Icon';
export default {
  components: {
    Icon,
  },
  props: ['href', 'icon_name', 'active', 'icon_type'],
  computed: {
    classes() {
      return this.active
        ? 'mt-3 p-2 text-blue-600 dark:text-blue-300 rounded-lg'
        : 'mt-3 p-2 hover:text-blue-600 dark-hover:text-blue-300 rounded-lg';
    },
  },
};
</script>