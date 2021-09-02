<template>
  <app-layout :status="rmsStatus">
    <template #header>
      <div
        class="bg-white p-4 shadow lg:flex lg:items-center lg:justify-between"
      >
        <div class="flex-1 min-w-0">
          <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap">
            <h2 class="flex text-xl font-bold sm:text-2xl">Members</h2>
          </div>
        </div>
      </div>
      <div
        class="bg-white p-2 border-t px-4 shadow flex items-center justify-start"
      >
        <search-filter v-model="search" />
      </div>
    </template>
    <template>
      <table class="w-full bg-white whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th
            v-for="column in columns"
            :key="column.value"
            class="px-6 pt-6 pb-4"
          >
            {{ column.text }}
          </th>
        </tr>
        <tr
          :key="item.id"
          v-for="item in members"
          class="hover:bg-gray-100 items-center focus-within:bg-gray-100"
        >
          <td
            v-for="column in columns"
            :key="column.value"
            class="border-t px-6 py-4"
          >
            <span v-if="column.type === 'array'">
              <span v-for="value of item[column.value]" :key="value">
                {{ value }}
              </span>
            </span>
            <span v-else-if="column.type === 'arrayObject'">
              <span
                v-for="value of item[column.value]"
                :key="value[column.internal]"
              >
                {{ value[column.internal] }}
              </span>
            </span>
            <span v-else>
              {{ item[column.value] }}
            </span>
          </td>
        </tr>
        <tr v-if="!members || members.length <= 0">
          <td class="no-items" :colspan="columns.length">No items to show.</td>
        </tr>
      </table>
      <pagination :links="links" />
    </template>
  </app-layout>
</template>

<script>
import AppLayout from './../Layouts/AppLayout';
import Pagination from './../Shared/Pagination';
import SearchFilter from './../Shared/SearchFilter';
import { createLinks } from '../Helpers/table.helpers';

let tsearch;
export default {
  components: {
    AppLayout,
    Pagination,
    SearchFilter,
  },
  data() {
    return {
      search: this.search || '',
      columns: [
        { text: 'Name', value: 'name', type: 'text' },
        {
          text: 'Phone Number',
          value: 'phones',
          type: 'arrayObject',
          internal: 'number',
        },
        {
          text: 'Email',
          value: 'emails',
          type: 'arrayObject',
          internal: 'address',
        },
      ],
    };
  },
  watch: {
    search: {
      handler: function (search) {
        clearTimeout(tsearch);
        // TODO: replace uri by current base url
        tsearch = setTimeout(() => {
          this.$inertia.replace(this.route('members', { search }));
        }, 500);
      },
      deep: true,
    },
  },
  computed: {
    links() {
      return createLinks('/members', this.pagination);
    },
  },
  props: {
    rmsStatus: Number,
    members: Array,
    pagination: Object,
  },
  created() {
    //console.log(this.members);
  },
};
</script>

<style scoped>
td {
  max-width: 20vw;
  text-overflow: ellipsis;
  overflow: hidden;
}
.no-items {
  text-align: center;
  padding: 2rem;
}
</style>