<template>
  <app-layout :status="crms.status">
    <template #header>
      <div class="bg-white p-4 shadow flex items-center justify-between">
        <h2 class="flex text-xl font-bold sm:text-2xl">Product Sub-Category List</h2>
        <button class="btn-icon bg-green-500" @click="create()">
          <i class="fas fa-plus"></i>
        </button>
      </div>
      <div
        class="bg-white p-2 border-t px-4 shadow flex items-center justify-start"
      >
        <search-filter v-model="query.search" />
      </div>
    </template>
    <template>
    
      <table class="w-full bg-white whitespace-no-wrap">
        <tr class="text-left">
    
  <template v-for="column in columns">
    <th
      class="p-4"
      @click="columnClick(column)"
      v-if="(column.value!='name_fr' && column.value!='name_en')"
      :key="column.value"
    >
      <i
        v-if="query.sort.column === column.value"
        :class="
          'fas fa-arrow-' + (query.sort.dir === 'asc' ? 'down' : 'up')
        "
      >
      </i>
      {{ column.text }}
    <th
      class="p-4"
      @click="columnClick(column)"
      v-else-if="(column.value=='name_fr' || column.value=='name_en') && site_url=='https://o3.mobilegiz.com'"
    >
    <i
        v-if="query.sort.column === column.value"
        :class="
          'fas fa-arrow-' + (query.sort.dir === 'asc' ? 'down' : 'up')
        "
      >
      </i>
      {{ column.text }}
    </th>
  </template>
    <th class="text-center"><span class="fas fa-cog"></span></th>
</tr>
        <tr :key="$i" v-for="(item, $i) in items" class="items-center">
  <template v-for="column in columns" >
    <td
      class="border-t p-4"
      v-if="(column.value!='name_fr' && column.value!='name_en')"
      :key="column.value"
    >
      
      <span v-if="column.type === 'date'">
        {{ item[column.value] | moment('YYYY-MM-DD') }}
      </span>
      <span v-else-if="column.type === 'image'">
        <img v-if="item[column.value]" :src="item[column.value]" />
        <img v-else src="/assets/images/default_product.png" />
      </span>
      <ul v-else-if="column.type === 'arrayObject'">
        <li
          v-for="value of item[column.value]"
          :key="value[column.internal]"
        >
          {{ value[column.internal] }}
        </li>
      </ul>
      <span v-else> {{ item[column.value] }} </span>
    <td
      class="border-t p-4"
      v-else-if="(column.value=='name_fr' || column.value=='name_en') && site_url=='https://o3.mobilegiz.com'"
    >
      <span v-if="column.type === 'date'">
        {{ item[column.value] | moment('YYYY-MM-DD') }}
      </span>
      <span v-else-if="column.type === 'image'">
        <img v-if="item[column.value]" :src="item[column.value]" />
        <img v-else src="/assets/images/default_product.png" />
      </span>
      <ul v-else-if="column.type === 'arrayObject'">
        <li
          v-for="value of item[column.value]"
          :key="value[column.internal]"
        >
          {{ value[column.internal] }}
        </li>
      </ul>
      <span v-else> {{ item[column.value] }} </span>
    </td> 
  </template>
    <td class="border-t">
      <div class="flex">
        <button
          class="btn-icon shadow"
          v-for="action of actions"
          :key="action.name"
          :disabled="blockUI"
          v-bind:class="action.classes"
          @click="action.callback(item)"
        >
          <i v-bind:class="'fas fa-' + action.icon"></i>
        </button>
      </div>
    </td>
  
</tr>
        <tr v-if="!items || items.length <= 0">
          <td class="no-items" :colspan="columns.length + 1">
            No items to show.
          </td>
        </tr>
      </table>
      <pagination :links="pagination" />
      <jet-dialog-modal :show="modal.show"  @close="closeModal()">
        <template #title>
          <h2>{{ modal.type || '' }} SubCategory</h2>
        </template>
        <template #content>
          <resource-form v-model="form" :site_url="site_url" v-if="modal.type" />
        </template>
        <template #footer>
          <jet-button @click.native="closeModal()"> Close </jet-button>
          <jet-button @click.native="submitForm()" class="bg-green-500">
            Submit
          </jet-button>
        </template>
      </jet-dialog-modal>
    </template>
  </app-layout>
</template>

<script>
import AppLayout from './../../Layouts/AppLayout';
import { createLinks } from './../../Helpers/table.helpers';
import Pagination from './../../Shared/Pagination';
import ResourceSelector from './../../Shared/ResourceSelector';
import SearchFilter from './../../Shared/SearchFilter';
import JetDialogModal from './../../Jetstream/DialogModal';
import JetButton from './../../Jetstream/Button';
import ResourceForm from './ResourceForm';
import { delay } from '../../Helpers/general.helpers';

export default {
  components: {
    AppLayout,
    Pagination,
    SearchFilter,
    JetButton,
    JetDialogModal,
    ResourceForm,
    ResourceSelector,
  },
  props: {
    crms: Object,
    items: Array,
    site_url: String,
    pagination: Array,
    params: Object,
  },
  data() {
    return {
      // REFACTOR
      blockUI: false,
      form: {},
      modal: {
        show: false,
        type: null,
      },
      columns: [
        { text: 'Main Category', value: 'p_group_name' },
        { text: 'Parent Category', value: 'subcats' },
        { text: 'Sub Category Name', value: 'name' },
        { text: 'Sub Category Name FR', value: 'name_fr' },
        { text: 'Sub Category Name EN', value: 'name_en' },
        { text: 'Description', value: 'description' },
      ],
      actions: [
        {
          name: 'delete',
          icon: 'trash',
          classes: 'bg-red-500',
          callback: this.delete,
        },
        {
          name: 'edit',
          icon: 'edit',
          classes: 'bg-blue-500',
          callback: this.edit,
        },
      ],
      query: {
        sort: this.params?.sort || {},
        search: this.params?.search || '',
      },
    };
  },
  watch: {
    query: {
      handler: delay(
        function (query) {
          this.$inertia.replace(this.route('subcategory.index', query));
        },
        200,
        'query.handler'
      ),
      deep: true,
    },
  },
  methods: {
    async load() {
      const query = { ...this.query };
      const params = Object.keys(query).length ? query : { remember: 'forget' };
      const url = this.route('subcategory', { ...params });
      this.$inertia.replace(url);
    },
    columnClick({ value: column, rules = [] }) {
      if (!rules.includes('no-sort')) {
        let dir = 'asc';
        if (this.query.sort?.column === column)
          dir = this.query.sort?.dir === 'asc' ? 'desc' : 'asc';
        this.query.sort = { column, dir };
      }
    },
    create() {
      this.form = {};
      this.modal = {
        show: true,
        type: 'create',
      };
    },
    edit(item) {
      this.form = {
        ...item,
        p_group: (item.p_group || []).map(({ id, name }) => ({
          text: name,
          value: id,
        })),
      };
      this.modal = {
        show: true,
        type: 'edit',
      };
    },
    delete(item) {
      this.$inertia.delete(
        this.route('subcategory.destroy', { subcategory: item.id })
      );
    },
    closeModal() {
      this.modal = {
        show: false,
        type: null,
      };
      this.form = {};
    },
    submitForm() {
      const data = { ...this.form };
      const id = data.id;
      delete data.id;
      const form = new FormData();
      for (const [k, v] of Object.entries(data)) {
        if (k === 'p_group'){
          for (const { value } of v || []) form.append(`${k}[]`, value);
        }
        else if (k === 'parent_id'){          
          if(v != null){
            form.append(`${k}`, v.id)          
          }
        }
        else {        
        form.append(k, v);
        }
      }

      switch (this.modal.type) {
        case 'create':
          this.$inertia.post(this.route('subcategory.store'), form, {
            onFinish: () => this.closeModal(),
          });
          break;
        case 'edit':
          form.append('_method', 'PUT');
          this.$inertia.post(
            this.route('subcategory.update', { subcategory: id }),
            form,
            {
              onFinish: () => this.closeModal(),
            }
          );
          break;
      }
    },
  },
};
</script>

<style scoped>
h2 {
  font-weight: bolder;
  width: 100%;
  text-align: center;
  text-transform: capitalize;
}

td img {
  object-fit: cover;
  border-radius: 100%;
  min-width: 42px;
  min-height: 42px;
  max-width: 42px;
  max-height: 42px;
}
th {
  cursor: pointer;
}
th i {
  font-size: 10px;
}
</style>