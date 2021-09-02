<template>
    <app-layout>
        <template #header>
            <div
                class="bg-white p-4 shadow lg:flex lg:items-center lg:justify-between"
            >
                <div class="flex-1 min-w-0">
                    <div
                        class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap"
                    >
                        <h2 class="flex text-xl font-bold sm:text-2xl">
                            Products
                        </h2>
                        <!-- Display if the current rms is setup and products has been synced -->
                        <template v-if="currentSetup === 3">
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
                                            class="block px-4 py-2 text-md text-gray-800 "
                                            v-for="item in columnHeaders"
                                            :key="item.value"
                                        >
                                            <label
                                                class="mt-2 flex items-center"
                                            >
                                                <input
                                                    type="checkbox"
                                                    class="form-checkbox"
                                                    :value="item.value"
                                                    v-model="selectedType"
                                                />
                                                <span class="ml-2">{{
                                                    item.text
                                                }}</span>
                                            </label>
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </div>
                            <div class="px-6 self-stretch">
                                <div class="h-full border-l-2"></div>
                            </div>
                            <div
                                class="flex items-center text-lg font-bold leading-5 sm:mr-6"
                            >
                                <search-filter v-model="form.search">
                                </search-filter>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Display if the current rms is setup and products has been synced -->
                <template v-if="currentSetup === 3">
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
                                    <div
                                        class="block px-4 py-2 text-md text-gray-800 "
                                        v-for="item in columnHeaders"
                                        :key="item.value"
                                    >
                                        <label class="mt-2 flex items-center">
                                            <input
                                                type="radio"
                                                class="form-radio"
                                                :value="item.value"
                                                v-model="currentSort"
                                            />
                                            <span class="ml-2">{{
                                                item.text
                                            }}</span>
                                        </label>
                                    </div>
                                </template>
                            </jet-dropdown>
                        </div>
                        <div class="px-6 self-stretch">
                            <div class="h-full border-l-2"></div>
                        </div>

                        <div
                            class="flex flex-col items-start text-lg leading-5 sm:mr-6"
                        >
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
                                Last updated {{ lastSyncedDate }}
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Current Rms Setting for the first time -->
        <div
            v-if="currentSetup === 1"
            class="mt-2 py-6 bg-white overflow-hidden shadow-xl p-4"
        >
            <p class="mb-8 leading-normal">
                Hey there! Welcome to Current Rms. Please contact our team if
                you need a demo to get around how the system works. Or setup
                your Current Rms account and get started.
            </p>
            <inertia-link
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                href="/user/profile"
            >
                Setup your Current Rms setting here
            </inertia-link>
        </div>

        <!-- Current Rms Setup but products not synced -->
        <div v-if="currentSetup === 2">
            <div class="mt-2 py-6 bg-white overflow-hidden shadow-xl p-4">
                <p class="mb-8 text-xl text-gray-500">
                    Please sync the records from Current RMS to get started. And
                    we will take it from there.
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
        </div>

        <!-- Current Rms Setup and Products Synced -->
        <template v-if="currentSetup === 3">
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

                    <tr
                        class="hover:bg-gray-100 items-center focus-within:bg-gray-100"
                        v-for="(item, $index) in products.data"
                        :key="$index"
                    >
                        <td
                            class="border-t px-6 py-4"
                            v-if="visibleColumns.includes('name')"
                        >
                            <text-input
                                type="text"
                                class="mt-1 block w-auto"
                                v-model="item.name"
                                autocomplete="name"
                            />
                        </td>

                        <!-- select type -->
                        <td
                            class="border-t px-6 py-4"
                            v-if="
                                visibleColumns.includes(
                                    'allowed_stock_type_name'
                                )
                            "
                        >
                            <select-input
                                v-model="item.allowed_stock_type"
                                :error="errors.allowed_stock_type"
                                class="mt-1 block w-auto"
                            >
                                <template v-for="option in allowedStockTypes">
                                    <option
                                        :value="option.id"
                                        :key="option.id"
                                        >{{ option.name }}</option
                                    >
                                </template>
                            </select-input>
                        </td>
                        <!-- end of select type -->

                        <!-- select type -->
                        <td
                            class="border-t px-6 py-4"
                            v-if="
                                visibleColumns.includes(
                                    'allowed_stock_type_name'
                                )
                            "
                        >
                            <select-input
                                v-model="item.stock_method"
                                :error="errors.stock_method"
                                class="mt-1 block w-auto"
                            >
                                <template v-for="option in stockMethodTypes">
                                    <option
                                        :value="option.id"
                                        :key="option.id"
                                        >{{ option.name }}</option
                                    >
                                </template>
                            </select-input>
                        </td>
                        <!-- end of select type -->

                        <td
                            class="border-t px-6 py-4"
                            v-if="visibleColumns.includes('weight')"
                        >
                            <text-input
                                type="text"
                                class="mt-1 block w-auto"
                                v-model="item.weight"
                                autocomplete="weight"
                            />
                        </td>
                        <td
                            class="border-t px-6 py-4"
                            v-if="visibleColumns.includes('product_group')"
                        >
                            <text-input
                                type="text"
                                class="mt-1 block w-auto"
                                v-model="item.product_group"
                                autocomplete="product_group"
                            />
                        </td>
                        <td
                            class="border-t px-6 py-4"
                            v-if="visibleColumns.includes('purchase_price')"
                        >
                            <text-input
                                type="text"
                                class="mt-1 block w-auto"
                                v-model="item.purchase_price"
                                autocomplete="purchase_price"
                            />
                        </td>
                        <td
                            class="border-t px-6 py-4"
                            v-if="visibleColumns.includes('sub_rental_price')"
                        >
                            <text-input
                                type="text"
                                class="mt-1 block w-auto"
                                v-model="item.sub_rental_price"
                                autocomplete="sub_rental_price"
                            />
                        </td>
                        <td
                            class="border-t px-6 py-4"
                            v-if="visibleColumns.includes('replacement_charge')"
                        >
                            <text-input
                                type="text"
                                class="mt-1 block w-auto"
                                v-model="item.replacement_charge"
                                autocomplete="replacement_charge"
                            />
                        </td>
                        <td
                            class="border-t px-6 py-4"
                            v-if="visibleColumns.includes('accessories')"
                        >
                            <text-input
                                type="text"
                                class="mt-1 block w-auto"
                                v-model="item.accessories"
                                autocomplete="accessories"
                            />
                        </td>
                        <td
                            class="border-t px-6 py-4"
                            v-if="
                                visibleColumns.includes('alternative_products')
                            "
                        >
                            <text-input
                                type="text"
                                class="mt-1 block w-auto"
                                v-model="item.alternative_products"
                                autocomplete="alternative_products"
                            />
                        </td>

                        <td class="border-t w-px">
                            <save-button
                                :loading="sending"
                                class="px-4 flex items-center"
                                type="submit"
                                >Save</save-button
                            >
                        </td>
                    </tr>
                    <tr v-if="products.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            No products found.
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="products.links" />
            <!-- End of the table to display the data or the products -->
        </template>
    </app-layout>
</template>

<script>
import Icon from "./../Shared/Icon";
import AppLayout from "./../Layouts/AppLayout";
import Pagination from "./../Shared/Pagination";
import JetDropdown from "./../Jetstream/Dropdown";
import LoadingButton from "./../Shared/LoadingButton";
import SearchFilter from "./../Shared/SearchFilter";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import SaveButton from "./../Shared/SaveButton";
import TextInput from "./../Shared/TextInput";
import SelectInput from "./../Shared/SelectInput";

export default {
    components: {
        AppLayout,
        LoadingButton,
        JetDropdown,
        Pagination,
        Icon,
        SearchFilter,
        SaveButton,
        TextInput,
        SelectInput
    },

    data() {
        return {
            sending: false,
            selectedType: [],
            columnHeaders: [
                { text: "Name", value: "name" },
                {
                    text: "Allowed Stock Type",
                    value: "allowed_stock_type_name"
                },
                { text: "Stock Method Name", value: "stock_method" },
                { text: "Weight", value: "weight" },
                { text: "Product Group", value: "product_group" },
                { text: "Purchase Price", value: "purchase_price" },
                { text: "Sub Rental Price", value: "sub_rental_price" },
                { text: "Replacement Charge", value: "replacement_charge" },
                { text: "Accessories", value: "accessories" },
                { text: "Alternative Products", value: "alternative_products" }
            ],
            form: {
                search: this.filters.search
            },
            currentSort: "name",
            currentSortDir: "asc"
        };
    },

    props: {
        products: Object,
        errors: Object,
        currentSetup: Number,
        lastSyncedDate: String,
        filters: Object,
        allowedStockTypes: Array,
        stockMethodTypes: Array
    },

    computed: {
        visibleColumns() {
            if (this.selectedType.length === 0) {
                return this.columnHeaders.map(c => c.value);
            }

            return this.selectedType;
        },

        sortedProducts() {
            return this.deserts.sort((a, b) => {
                let modifier = 1;
                if (this.currentSortDir === "desc") modifier = -1;
                if (a[this.currentSort] < b[this.currentSort])
                    return -1 * modifier;
                if (a[this.currentSort] > b[this.currentSort])
                    return 1 * modifier;
                return 0;
            });
        }
    },

    watch: {
        form: {
            handler: throttle(function() {
                let query = pickBy(this.form);
                this.$inertia.replace(
                    this.route(
                        "dashboard",
                        Object.keys(query).length
                            ? query
                            : { remember: "forget" }
                    )
                );
            }, 150),
            deep: true
        },

        currentSort(s) {
            //if s == current sort, reverse
            if (s === this.currentSort) {
                this.currentSortDir =
                    this.currentSortDir === "asc" ? "desc" : "asc";
            }

            this.currentSort = s;
        }
    },

    methods: {
        syncProducts: function() {
            this.$inertia.visit(this.route("products.sync"), {
                method: "get",
                onStart: () => (this.sending = true),
                onFinish: () => (this.sending = false)
            });
        }
    }
};
</script>
