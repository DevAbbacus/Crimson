<template>
  <app-layout :status="crms.status">
    <template #header>
      <div
        class="bg-white p-4 shadow lg:flex lg:items-center lg:justify-between top_header"
      >
        <div class="flex-1 min-w-0">
          <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap">
            <h2 class="flex text-xl font-bold sm:text-2xl">Products</h2>
            <!-- Display if the current rms is setup and products has been synced -->
            <template v-if="crms.status === 3">
              <div class="px-6 self-stretch">
                <div class="h-full border-l-2"></div>
              </div>
              <div class="flex items-center">
                <!-- Drop down filter to filter columns -->
                <jet-dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      class="flex text-lg leading-5 font-bold border-none focus:outline-none focus:text-red-500 hover:text-red-500 active:text-red-500 transition duration-150 ease-in-out"
                    >
                      <svg
                        class="flex-shrink-0 mr-2 h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                        ></path>
                      </svg>
                      Columns to display
                    </button>
                  </template>

                  <template #content>
                    <!-- Account Management -->
                    <div
                      class="block px-4 py-2 text-md text-gray-800"
                      v-for="item in columnHeaders"
                      :key="item.value"
                      v-if="item.value != null"
                    >
                    <div>
                      <label class="mt-2 flex items-center">
                        <input
                          type="checkbox"
                          class="form-checkbox"
                          :value="item.value"
                          v-model="displayedColumns"
                        />
                        <span class="ml-2">{{ item.text }}</span>
                      </label>
                      </div>
                    </div>
                  </template>
                </jet-dropdown>
              </div>
              <div class="px-6 self-stretch">
                <div class="h-full border-l-2"></div>
              </div>
              <!-- drop down section to filter coulmns -->
              <div class="flex items-center">
                <!-- Drop down filter to filter columns -->
                <jet-dropdown align="left" width="max-w-md">
                  <template #trigger>
                    <button
                      class="flex text-lg leading-5 font-bold border-none focus:outline-none focus:text-red-500 hover:text-red-500 active:text-red-500 transition duration-150 ease-in-out"
                    >
                      <svg
                        class="flex-shrink-0 mr-2 h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                        ></path>
                      </svg>
                      Select Filters
                    </button>
                  </template>

                  <template #content>
                    <!-- Filter Management -->
                    <div class="p-6">
                      <div class="flex justify-between mb-8">
                        <div class="mr-6">
                          <p class="font-sans text-lg text-gray-800">
                            Categories
                          </p>
                          <div
                            class="mt-4 w-48 h-64 overflow-y-auto border border-gray-500 p-4"
                          >
                            <label
                              v-for="item in productGroups"
                              class="mt-2 flex items-center"
                              :key="item.id"
                              v-if="item.id !== null"
                            >
                              <input
                                type="checkbox"
                                class="form-checkbox"
                                :value="item.id"
                                v-model="query.groups"
                              />
                              <span class="ml-2">{{ item.name }}</span>
                            </label>
                          </div>
                        </div>
                        <div>
                          <p class="font-sans text-lg text-gray-800">
                            Blank Field
                          </p>
                          <div
                            class="mt-4 p-4 w-48 h-64 overflow-y-auto border border-gray-500"
                          >
                            <template v-for="item in columnHeaders" v-if="item.text !== null">
                              <label
                                v-if="!item.notsorteable"
                                class="mt-2 flex items-center"
                                :key="item.id"
                              >
                                <input
                                  v-if="item.text !== 'Image'"
                                  type="checkbox"
                                  class="form-checkbox"
                                  :value="item.filterField || item.value"
                                  v-model="query.blanks"
                                />
                                <span
                                  v-if="item.text !== 'Image'"
                                  class="ml-2"
                                  >{{ item.text }}</span
                                >
                              </label>
                            </template>
                          </div>
                        </div>
                      </div>

                      <!-- Buttons section -->
                      <div class="flex justify-center">
                        <button
                          @click="resetFilters()"
                          type="button"
                          class="mr-2 rounded-full border-2 border-red-500 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                        >
                          Reset
                        </button>
                        <button
                          @click="applyFilters()"
                          type="button"
                          class="rounded-full border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                        >
                          Apply
                        </button>
                      </div>
                    </div>
                  </template>
                </jet-dropdown>
              </div>
              <div class="px-6 self-stretch">
                <div class="h-full border-l-2"></div>
              </div>
              
              <label class="mt-2 flex items-center" id="select_site" style="padding: 0 11px 7px 0; display:none">
                {{(user_type == 'Wordpress') ?  'Wordpress' : 'Magento'}}
              </label>
              

              <div class="flex items-center">
                <!-- Drop down filter to filter columns -->
                <jet-dropdown align="left" width="max-w-md">
                  <template #trigger>
                    <button
                        @click="syncProductsToMage($page.user.id)"
                        type="button" v-bind:disabled="isSyncing"
                        class="mr-2 rounded-full border-2 border-red-500 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                        >
                        Sync Products to Wordpress  <i class="fa fa-circle-o-notch fa-spin spinner " :class="isSyncing ? '' : 'hide'"></i>
                        </button>
                    </template>
                </jet-dropdown>
              </div>

              <div class="resync_product">
                  <div class="">
                      <loading-button
                        :loading="sending"
                        class="btn-indigo"
                        @click.native="syncProducts"
                        style="background-color: teal;margin-top: 2px;height: 38px;border-radius: 25px;" >Sync Data from Current Rms</loading-button
                      >
                  </div>
              </div>

              <div
                class="flex items-center text-lg font-bold leading-5 sm:mr-6 mt-2"
              >
                <search-filter v-model="query.search"> </search-filter>
              </div>
            </template>

          </div>
        </div>

        <!-- Display if the current rms is setup and products has been synced -->
        <template v-if="crms.status === 3">
          <div class="mt-5 flex lg:mt-0 lg:ml-4">
            <div class="flex items-center sm:mr-6">
              <!-- Drop down to sort columns -->
              <jet-dropdown align="right" width="48">
                <template #trigger>
                  <button
                    class="flex text-lg leading-5 font-bold border-none focus:outline-none focus:text-red-500 hover:text-red-500 active:text-red-500 transition duration-150 ease-in-out"
                  >
                    <svg
                      class="flex-shrink-0 mr-2 h-5 w-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                      ></path>
                    </svg>
                    Sort
                  </button>
                </template>

                <template #content>
                  <template v-for="item in columnHeaders">
                    <div
                      class="block px-4 py-2 text-md text-gray-800"
                      :key="item.value"
                      v-if="!item.notsorteable"
                    >
                      <label class="mt-2 flex items-center">
                        <input
                          type="radio"
                          class="form-radio"
                          :value="item.value"
                          v-model="query.sort.column"
                        />
                        <span class="ml-2">{{ item.text }}</span>
                      </label>
                    </div>
                  </template>
                </template>
              </jet-dropdown>
            </div>
            <div class="px-6 self-stretch">
              <div class="h-full border-l-2"></div>
            </div>

            <div class="flex flex-col items-start text-lg leading-5 sm:mr-6">
              <div class="flex text-green-700 font-bold">
                <svg
                  class="flex-shrink-0 mr-2 h-5 w-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  ></path>
                </svg>
                Synced
              </div>
              <div class="text-sm leading-5 text-gray-500">
                Last updated {{ crms.lastSync }}
              </div>
            </div>
          </div>
        </template>
      </div>
    </template>

    <!-- Current Rms Setting for the first time -->
    <div
      v-if="crms.status === 1"
      class="mt-2 py-6 bg-white overflow-hidden shadow-xl p-4"
    >
      <p class="mb-8 leading-normal">
        Hey there! Welcome to Current Rms. Please contact our team if you need a
        demo to get around how the system works. Or setup your Current Rms
        account and get started.
      </p>
      <inertia-link
        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
        href="/user/profile"
      >
        Setup your Current Rms setting here
      </inertia-link>
    </div>

    <!-- Current Rms Setup but products not synced -->
    <div v-if="crms.status === 2">
      <div class="mt-2 py-6 bg-white overflow-hidden shadow-xl p-4">
        <p class="mb-8 text-xl text-gray-500">
          Please sync the records from Current RMS to get started. And we will
          take it from there.
        </p>

        <div class="mt-5">
          <loading-button
            :loading="sending"
            class="btn-indigo"
            @click.native="syncProducts"
            >Sync Data from Current Rms</loading-button
          >
        </div>
      </div>
    </div><div class="mt-2" v-if="mgSync"><!---->
      <div class="mb-8 flex items-center justify-between bg-green-500 rounded">
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-4 mr-2 flex-shrink-0 w-4 h-4 fill-white">
            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"></path>
          </svg>
          <div class="py-4 text-white text-sm font-medium">
            {{mgSync}}
          </div>
        </div>
        <button type="button" class="group mr-2 p-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908" class="block w-2 h-2 fill-red-800 group-hover:fill-white">
            <path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z"></path>
          </svg>
        </button>
      </div> <!---->
    </div>


    <!-- Current Rms Setup and Products Synced -->
    <template v-if="crms.status === 3">
      <!-- Start of the table to display the data or the products -->
      <div class="mt-2 overflow-x-auto">
        <!-- {{ products }} -->
        <table class="w-full bg-white whitespace-no-wrap">
          <tr class="text-left font-bold">
            <template v-for="item in columnHeaders">
              <th
                class="px-6 pt-6 pb-4"
                v-if="visibleColumns.includes(item.value)"
                :key="item.value"
              >
                {{ item.text }}
              </th>
            </template>
          </tr>

          <table-row
            v-for="item in items || []"
            :key="item.id"
            :product="item"
            :stockTypes="allowedStockTypes"
            :stockMethods="stockMethodTypes"
            :columns="visibleColumns"
            :pGroups="productGroups"
            :rGroups="revenueGroups"
            :cGroups="costGroups"
            :srDefinitions="rateDefinitions"
            :customHeaders="customHeaders"
          />
          <tr v-if="items.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No products found.</td>
          </tr>
        </table>
      </div>
      <pagination :links="pagination" />
      <!-- End of the table to display the data or the products -->
    </template>
  </app-layout>
</template>

<script>
import Icon from './../Shared/Icon';
import AppLayout from './../Layouts/AppLayout';
import Pagination from './../Shared/Pagination';
import JetDropdown from './../Jetstream/Dropdown';
import LoadingButton from './../Shared/LoadingButton';
import SearchFilter from './../Shared/SearchFilter';
import pickBy from 'lodash/pickBy';
import throttle from 'lodash/throttle';
import TableRow from './../Shared/TableRow';
import FilterColumns from './../Shared/FilterColumns';

export default {
  components: {
    AppLayout,
    LoadingButton,
    JetDropdown,
    Pagination,
    Icon,
    SearchFilter,
    TableRow,
    FilterColumns,
  },

  remember: {
    data: 'forget',
    props: 'forget',
  },


  data() {
    return {
      sending: false,
      isSyncing:false,
      mgSync:'',
      displayedColumns: [],

      columnHeaders:  [
        { text: 'Icon', value: 'icon', notsorteable: true },
        { text: 'Name', value: 'name' },
        { text: 'Description', value: 'description' },
        { text: 'Stock Method Name', value: 'stock_method' },
        { text: 'Weight', value: 'weight' },
        { text: 'Accessory Only', value: 'accessory_only' },
        {
          text: 'Product Group',
          value: 'product_group',
          filterField: 'product_group_id',
          notsorteable: true,
        },
        {
          text: 'Sub Category',
          value: 'p_group',
          notsorteable: true,
        },
        {
          text: 'Allowed Stock Type',
          value: 'allowed_stock_type',
        },
        // { text: "Revenue Group", value: "revenue_group", filterField: 'rental_revenue_group' },
        // { text: "Cost Group", value: "cost_group" },
        { text: 'Rates', value: 'rates', notsorteable: true },
        // { text: "Purchase Price", value: "price" },
        { text: 'Replacement Charge', value: 'replacement_charge' },
        { text: 'Notes', value: 'notes' },
        {
          text: 'Other Accessories',
          value: 'alternative_products',
          notsorteable: true,
        },
        {
          text: 'Accessories',
          value: 'alternative_products',
          notsorteable: true,
        },
        
      ],

      query: {
        sort: this.params?.sort || {},
        search: this.params?.search,
        blanks: this.params?.blanks || [],
        groups: this.params?.groups || [],
      },
      customHeaders:
      [
        {
          text: 'product_height_mm',
          value: 'product_height_mm',
          notsorteable: true,
        },{
          text: 'product_width_mm',
          value: 'product_width_mm',
          notsorteable: true,
        },{
          text: 'weight_kg',
          value: 'weight_kg',
          notsorteable: true,
        },{
          text: 'specname_1',
          value: 'specname_1',
          notsorteable: true,
        },{
          text: 'specvalue_1',
          value: 'specvalue_1',
          notsorteable: true,
        },{
          text: 'specname_2',
          value: 'specname_2',
          notsorteable: true,
        },{
          text: 'specvalue_2',
          value: 'specvalue_2',
          notsorteable: true,
        },{
          text: 'specname_3',
          value: 'specname_3',
          notsorteable: true,
        },{
          text: 'specvalue_3',
          value: 'specvalue_3',
          notsorteable: true,
        },{
          text: 'specname_4',
          value: 'specname_4',
          notsorteable: true,
        },{
          text: 'specvalue_4',
          value: 'specvalue_4',
          notsorteable: true,
        },{
          text: 'specname_5',
          value: 'specname_5',
          notsorteable: true,
        },{
          text: 'specvalue_5',
          value: 'specvalue_5',
          notsorteable: true,
        },{
          text: 'specname_6',
          value: 'specname_6',
          notsorteable: true,
        },
        {
          text : 'specvalue_6' ,
          value:'specvalue_6',
          notsorteable:true,
        },
        {
          text : 'colour_temperature'  ,
          value:'colour_temperature',
          notsorteable:true,
        },
        {
          text : 'power_type'  ,
          value:'power_type',
          notsorteable:true,
        },
        {
          text : 'output_at_8m'  ,
          value:'output_at_8m',
          notsorteable:true,
        },
        {
          text : 'output_at_5m'  ,
          value:'output_at_5m',
          notsorteable:true,
        },
        {
          text : 'output_at_2m'  ,
          value:'output_at_2m',
          notsorteable:true,
        },
        {
          text : 'power_input_watts' ,
          value:'power_input_watts',
          notsorteable:true,
        },
        {
          text : 'optional_accessory_1'  ,
          value:'optional_accessory_1',
          notsorteable:true,
        },
        {
          text : 'optional_accessory_2'  ,
          value:'optional_accessory_2',
          notsorteable:true,
        },
        {
          text : 'optional_accessory_3'  ,
          value:'optional_accessory_3',
          notsorteable:true,
        },
        {
          text : 'optional_accessory_4'  ,
          value:'optional_accessory_4',
          notsorteable:true,
        },
        {
          text : 'discount_start'  ,
          value:'discount_start',
          notsorteable:true,
        },
        {
          text : 'discount_end'  ,
          value:'discount_end',
          notsorteable:true,
        },
        {
          text : 'discount_percentage',
          value:'discount_percentage',
          notsorteable:true,
        },
        {
          text : 'discount_amount',
          value:'discount_amount',
          notsorteable:true,
        },
        {
          text : 'lighthouse_category',
          value:'lighthouse_category',
          notsorteable:true,
        },
        {
          text : 'alternate_search_terms',
          value:'alternate_search_terms',
          notsorteable:true,
        },
        {
          text : 'when_booked_separately',
          value:'when_booked_separately',
          notsorteable:true,
        },
        {
          text : 'lighthouse_sort_order',
          value:'lighthouse_sort_order',
          notsorteable:true,
        },
        {
          text : 'usability',
          value:'usability',
          notsorteable:true,
        },
        {
          text : 'gaffer_tips',
          value:'gaffer_tips',
          notsorteable:true,
        },
        {
          text : 'gaffer_notes',
          value:'gaffer_notes',
          notsorteable:true,
        },
        {
          text : 'prep_tasks',
          value:'prep_tasks',
          notsorteable:true,
        },
        {
          text : 'post_tasks',
          value:'post_tasks',
          notsorteable:true,
        }]
    };
  },

  props: {
    // used
    crms: Object,
    items: Array,
    pagination: Array,
    params: Object,
    custom_field_columns: Array,
    user_type: String,

    // optimize
    allowedStockTypes: Array,
    stockMethodTypes: Array,
    productGroups: Array,
    revenueGroups: Array,
    costGroups: Array,
    rateDefinitions: Array,
  },

  computed: {
    visibleColumns() {
      var index;
      if(this.custom_field_columns != null && this.custom_field_columns != undefined ){
      for (index = 0; index < this.custom_field_columns.length; ++index) {
        this.columnHeaders.push(
          { text: this.custom_field_columns[index], value: this.custom_field_columns[index] }
          )
      }}
      if (this.displayedColumns?.length === 0) {
        return this.columnHeaders.map((c) => c.value);
      }
      return this.displayedColumns;
    },
  },

  watch: {
    'query.search': throttle(function () {
      this.load();
    }, 150),
    'query.sort': {
      handler: throttle(function () {
        this.load();
      }, 150),
      deep: true,
    },
  },

  methods: {
    load() {
      const query = { ...this.query };
      const params = Object.keys(query).length ? query : { remember: 'forget' };
      const url = this.route('dashboard', { ...params });
      this.$inertia.replace(url);
    },
    syncProducts: function () {
      this.$inertia.visit(this.route('products.sync'), {
        method: 'get',
        onStart: () => (this.sending = true),
        onFinish: () => (this.sending = false),
        errorBag: () => (console.info("here error come")),
      });
    },
    syncProductsToMage(id) {
      this.isSyncing = true;
      console.info("MgSync :- ", this.Sync);
          var site = document.getElementById('select_site').innerHTML;
          site = site.trim(site);
          if(site == 'Magento')
              var url = axios.get('/api/syncMgaeProducts/'+id);
            else
              var url = axios.get('/api/syncWordpressProducts/'+id);
           
          url.then((response)=>{
            this.mgSync = response.data;
            this.isSyncing = false;
            // alert(ab.message);
          }).catch(e=>{
            this.isSyncing = false;
            this.mgSync = "Your product sync is under process it will take some more time!";
          });
    },
    resetFilters() {
      this.query.blanks = [];
      this.query.groups = [];
      this.load();
    },
    applyFilters() {
      const { blanks = [], groups = [] } = this.query || {};

      if (groups.length + blanks.length === 0) this.resetFilters();
      else this.load();
    },
  },

  created(){
    if(this.custom_field_columns != undefined){
      Object.keys(this.custom_field_columns).forEach(key => {
        this.columnHeaders.push({ 'text' : key.replace(/_/g, ' ').replace(/(^\w{1})|(\s+\w{1})/g, letter => letter.toUpperCase()), 'value' :  key, 'notsorteable'  :true});
      })
    }
  }
};
</script>
