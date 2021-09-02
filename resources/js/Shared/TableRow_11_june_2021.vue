<template>
  <tr class="hover:bg-gray-100 items-center focus-within:bg-gray-100">
    <td class="border-t px-6 py-4" v-if="columns.includes('icon')">
      <div class="image-picker" :style="cssvars">
        <label >
          <img v-if="form.images.length > 0" :src="form.images[0].path" />
          <img v-else-if="form.icon != ''" :src="form.icon" />
          <i v-else class="fas fa-image"></i>
        </label>
        <input type="file" ref="file" multiple="multiple" @change="updateImage(e)">
      </div>
      <button class="bg-green-500 view-images" @click="viewImages()">
          View Images ({{ (form.images.length > 0) ? (form.images.length) : (form.icon != '' ? 1 : 0) }})
        </button>
        <template>
          <jet-dialog-modal :show="modal.show" @close="closeModal()">
            <template #title>
              <h2><b>{{ form.name }} Images</b></h2>
            </template>
            <template #content>
              <ol :key="form.images.length">
                <template v-for="image in form.images" >
                  <li>
                    <img :src="image.path" class="product-image" >
                    <button class="btn-icon shadow bg-red-500" @click="deleteImage(form.id, image.id)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </li>
                </template>
                <li v-if="form.images.length === 0">
                  <b class="border-t px-6 py-4">No images found.</b>
                </li>
              </ol>
            </template>
            <template #footer>
              <jet-button @click="closeModal()"> Close </jet-button>
            </template>
          </jet-dialog-modal>
        </template>
    </td>

    <td class="border-t px-6 py-4" v-if="columns.includes('name')">
      <text-input v-model.trim="form.name" class="mt-1 block w-auto" />
      <div
        v-if="!$v.form.name.required"
        class="mt-2 error text-sm text-red-600"
      >
        This field is required
      </div>
    </td>

    <td class="border-t px-6 py-4" v-if="columns.includes('description')">
      <textarea
        class="form-input resize border rounded-none bg-gray-100 focus:outline-none focus:shadow-outline"
        v-model="form.description"
        rows="4"
      ></textarea>
    </td>

    <td class="border-t px-6 py-4" v-if="columns.includes('stock_method')">
      <select-input v-model="form.stock_method" class="mt-1 block w-auto">
        <template v-for="option in stockMethods">
          <option :value="option.id" :key="option.id">{{ option.name }}</option>
        </template>
      </select-input>
    </td>

    <td class="border-t px-6 py-4" v-if="columns.includes('weight')">
      <text-input v-model="form.weight" class="mt-1 block w-auto" />
    </td>

    <td class="border-t px-6 py-4" v-if="columns.includes('product_group')">
      <select-input v-model="form.product_group_id" class="mt-1 block w-auto">
        <!-- <option :value="null" /> -->
        <template v-for="option in pGroups">
          <option :value="option.id" :key="option.id">{{ option.name }} .. </option>
        </template>
      </select-input>
    </td>

    <td
      class="border-t px-6 py-4"
      v-if="columns.includes('p_group')"
    >
      <!-- <sub-category-selector
        :loader="subcategorylist"
        v-model="form.p_group"
        direction="top"
        placeholder="Category"
      /> -->
      <div class="selected-options">
        <div
          class="selected"
          v-for="selection of form.p_group"
          :key="selection.value"
        >
          <span> {{ selection.text }} </span>
          <button
            class="btn-icon bg-red-500"
            type="button"
            @click="remove(selection)"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <multiselect
        :options="multiselectorData"
        :close-on-select="true"
        :clear-on-select="false"
        :preserve-search="true"
        placeholder="Sub Category"
        label="name"
        id="example"
        @select="onSelect"
        group-values="childs"
        group-label="name"
        class="category-selector"
        :loading="loading" :hideSelected="true"
        @open="loadSubCat(search)"
      >
      </multiselect>
    </td>

    <td
      class="border-t px-6 py-4"
      v-if="columns.includes('allowed_stock_type')"
    >
      <select-input v-model="form.allowed_stock_type" class="mt-1 block w-auto">
        <option :value="null" />
        <template v-for="option in stockTypes">
          <option :value="option.id" :key="option.id">{{ option.name }}</option>
        </template>
      </select-input>
    </td>

    <!-- <td class="border-t px-6 py-4" v-if="columns.includes('revenue_group')">
      <template v-if="form.allowed_stock_type != 2">
        <select-input
          v-model="form.rental_revenue_group"
          class="block w-auto"
          label="Rental Revenue Group"
        >
          <option :value="null" />
          <template v-for="option in rGroups">
            <option :value="option.id" :key="option.id">
              {{ option.name }}
            </option>
          </template>
        </select-input>
        <div
          v-if="!$v.form.rental_revenue_group.required"
          class="mt-2 error text-sm text-red-600"
        >
          This field is required
        </div>
      </template> -->

    <!-- <template v-if="form.allowed_stock_type != 1">
        <select-input
          v-model="form.sale_revenue_group"
          class="block w-auto"
          label="Sale Revenue Group"
        >
          <option :value="null" />
          <template v-for="option in rGroups">
            <option :value="option.id" :key="option.id">
              {{ option.name }}
            </option>
          </template>
        </select-input>
      </template> -->
    <!-- </td> -->
    <!-- <td class="border-t px-6 py-4" v-if="columns.includes('cost_group')">
      <template v-if="form.allowed_stock_type != 2">
        <select-input
          v-model="form.sub_rental_cost_group"
          class="block w-auto"
          label="Sub Rental Cost Group"
        >
          <option :value="null" />
          <template v-for="option in cGroups">
            <option :value="option.id" :key="option.id">
              {{ option.name }}
            </option>
          </template>
        </select-input>
        <div
          v-if="!$v.form.sub_rental_cost_group.required"
          class="mt-2 error text-sm text-red-600"
        > -->
    <!-- This field is required -->
    <!-- </div>
      </template> -->

    <!-- <template v-if="form.allowed_stock_type != 1">
        <select-input
          v-model="form.purchase_cost_group"
          class="block w-auto"
          label="Purchase Cost Group"
        >
          <option :value="null" />
          <template v-for="option in cGroups">
            <option :value="option.id" :key="option.id">
              {{ option.name }}
            </option>
          </template>
        </select-input>
      </template>
    </td> -->
    <td class="border-t px-6 py-4" v-if="columns.includes('rates')">
      <div v-for="rate of form.rates" :key="rate.id">
        <p>{{ rate.transaction_type == 1 ? 'Rental' : 'Sales' }}</p>
        <input
          type="number"
          v-model="rate.price"
          class="form-input bg-gray-100 rounded-none shadow-sm"
        />
        <br />
      </div>
    </td>
    <!-- <td class="border-t px-6 py-4" v-if="columns.includes('price')"> -->
    <!-- <select-input
        v-if="form.allowed_stock_type != 2"
        v-model="form.sub_rental_rate_definition"
        class="block w-auto"
        label="Rate Definition"
      >
        <option :value="null" />
        <template v-for="option in srDefinitions">
          <option :value="option.id" :key="option.id">{{ option.name }}</option>
        </template>
      </select-input> -->

    <!-- <template>
        <text-input
          v-if="form.allowed_stock_type != 1"
          v-model="form.purchase_price"
          class="mt-1 block w-auto"
          label="Purchase Price"
        />
        <div
          v-if="!$v.form.purchase_price.required"
          class="mt-2 error text-sm text-red-600"
        >
          This field is required
        </div>
      </template> -->

    <!-- <template>
        <text-input
          v-if="form.allowed_stock_type != 2"
          v-model="form.sub_rental_price"
          class="mt-1 block w-auto"
          label="Sub Rental Price"
        />
        <div
          v-if="!$v.form.sub_rental_price.required"
          class="mt-2 error text-sm text-red-600"
        >
          This field is required
        </div>
      </template> -->

    <!--
        <template>
        <text-input
          class="mt-1 block w-auto"
          label="Rate Category Price"
        />
        <div
        class="mt-2 error text-sm text-red-600"
        >
        </div>
      </template>
      -->
    <!-- </td> -->

    <!--
    <td class="border-t px-6 py-4" v-if="columns.includes('rates')">
      <div v-for="rates in form.rates" :key="rates.id">
      <option
        :key="rates.id"
        :value="rates.id"
        class="form-input bg-gray-100 rounded-none shadow-sm"
      >
      {{ rates.price }}
      </option>
       </div>
    </td>-->

    <td
      class="border-t px-6 py-4"
      v-if="columns.includes('replacement_charge')"
    >
      <text-input
        v-model="form.replacement_charge"
        class="mt-1 block w-auto"
      />
    </td>
    

    <td class="border-t px-6 py-4" v-if="columns.includes('notes')">
      <textarea
        class="form-input resize border rounded-none bg-gray-100 focus:outline-none focus:shadow-outline"
        v-model="form.notes"
        rows="4"
      ></textarea>
    </td>


    <!--
    <td class="border-t px-6 py-4" v-if="columns.includes('accessories')">
      <text-input
        v-model="form.accessories"
        class="mt-1 block w-auto"
      />
    </td>
    -->

    <td
      class="border-t px-6 py-4"
      v-if="columns.includes('alternative_products')"
    >
      <resource-selector
        :loader="loadProducts"
        v-model="form.alternative_products"
        direction="bottom"
        placeholder="Products"
      />
    </td>

    <!-- <tr > -->
    <!-- <div > -->
      
      <td v-for="(value, key) of form.custom_fields" :key="key" class="border-t px-6 py-4" 
      v-if="columns.includes(key)"
       >
        <text-input v-model="form.custom_fields[key]" class="mt-1 block w-auto" />
      </td>
    <!-- </div> -->
    <!-- </tr> -->

    <td class="border-t w-px">
      <button
        :disabled="sending"
        type="submit"
        @click="submit()"
        class="inline-flex items-center justify-center px-4 py-2 bg-red-500 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-600 transition ease-in-out duration-150"
      >
        <div v-if="sending" class="btn-spinner mr-2" />
        Save
      </button>
    </td>

  </tr>
</template>

<script>
import ImagePicker from './ImagePicker';
import ResourceSelector from './ResourceSelector';
import TextInput from './TextInput';
import SelectInput from './SelectInput';
import JetInputError from '../Jetstream/InputError';
import JetDialogModal from '../Jetstream/DialogModal';
import { required } from 'vuelidate/lib/validators';
import { formByJson } from '../Helpers/form.helpers';
import CategorySelector from './CategorySelector';
import SubCategorySelector from './SubCategorySelector';
import Vue from 'vue';

export default {
  components: {
    ImagePicker,
    ResourceSelector,
    TextInput,
    SelectInput,
    JetInputError,
    JetDialogModal,
    CategorySelector,
    SubCategorySelector,
  },
  data() {
    return {
      products: [],
      rateCategory: [],
      isLoadingAlternatives: false,
      // olds
      validationActive: false,
      sending: false,
      modal: {
        show: false,
      },
      form: {
        id: this.product.id,
        icon: this.product.icon,
        name: this.product.name,
        description: this.product.description,
        stock_method: this.product.stock_method,
        weight: this.product.weight,
        product_group_id: this.product.product_group_id,
        purchase_price: this.product.purchase_price,
        sub_rental_price: this.product.sub_rental_price,
        allowed_stock_type: this.product.allowed_stock_type,
        replacement_charge: this.product.replacement_charge,
        notes: this.product.notes,
        //accessories: this.product.accessories,
        rental_revenue_group: this.product.rental_revenue_group,
        sale_revenue_group: this.product.sale_revenue_group,
        sub_rental_cost_group: this.product.sub_rental_cost_group,
        sub_rental_rate_definition: this.product.sub_rental_rate_definition,
        purchase_cost_group: this.product.purchase_cost_group,
        rates: this.product.rates || [],
        alternative_products: this.product.alternative_products,
        custom_fields: this.product.custom_fields,
        p_group: this.product.p_group,
        images: this.product.images || [],
      },
      options: ['list', 'of', 'options'],
      selecteds:[],
      multiselectorData:[],
      cssvars:'',
      search: '',
      loading:false
    };
  },
  validations: {
    form: {
      name: {
        required,
      }
    },
  },
  props: {
    product: Object,
    columns: Array,
    stockTypes: Array,
    stockMethods: Array,
    pGroups: Array,
    rGroups: Array,
    cGroups: Array,
    srDefinitions: Array,
    customHeaders:Array
  },
  methods: {
    limitText(count) {
      return `and other ${count}.`;
    },
    viewImages() {
      this.modal = {
        show: true,
      };
    },
    deleteImage(product_id, image_id) {
      this.$inertia.delete(
        this.route('products.delete.image', { product_id: product_id, image_id: image_id })
      );
    },
    closeModal() {
      this.modal = {
        show: false,
      };
    },
    submit() {
      this.$v.$touch();
      if (this.$v.$invalid) return;
      const form = formByJson(this.form, {
        objResolver: (k, obj) => {
          if (k === 'alternative_products') return obj?.value;
          if (typeof obj === 'object') return JSON.stringify(obj);
          return obj;
        },
        objResolver: (k, obj) => {
          if (k === 'p_group') return obj?.value;
          if (typeof obj === 'object') return JSON.stringify(obj);
          return obj;
        },
      });
      form.append('_method', 'put');
      for( var i = 0; i < this.$refs.file.files.length; i++ ){
        let file = this.$refs.file.files[i];
        form.append('files[' + i + ']', file);
      }
      this.$inertia.post(this.route('products.update', this.product.id), form, {
        onStart: () => (this.sending = true),
        onFinish: () => (this.sending = false),
        onSuccess: () => (this.form.images = this.product.images),
      });
    },
    log(l) {
      console.log(l);
    },
    async loadProducts(search) {
      const uri = `/productslist?search=${search}&columns=['id','name']`;
      const response = await fetch(uri);
      const result = await response.json();
      const alternative_products = result.items;
      return alternative_products.map(({ id, name }) => ({
        value: id,
        text: name,
      }));
    },
    async subcategorylist(search) {
      const uri = `/subcategorylist?search=${search}&columns=['id','name']`;
      const response = await fetch(uri);
      const result = await response.json();
      const p_group = result.items;
      return p_group.map(({ id, name }) => ({
        value: id,
        text: name,
      }));
    },
    async updateImage() {
      /*const file = (this.$refs.file.files || [])[0];
      const fr = new FileReader();
      fr.onload = (e) => {
        this.form.images[0].id = 99999;
        this.form.images[0].path = e.srcElement.result;
      };
      if (file) fr.readAsDataURL(file);*/
    },
    remove(selection) {
      this.form.p_group = this.form.p_group.filter(
        (i) => i.value !== selection.value
      );
      for (var index in this.form.p_group) {
        this.selecteds.push(this.form.p_group[index].value);
      }
    },
    onSelect (option, id) {
      this.search = '';
      if(this.selecteds.indexOf(option.id) == -1){
        this.form.p_group.push({
              value: option.id,
              text: option.name,
            });
        this.selecteds.push(option.id);
      }
    },
    async loadSubCat(){
      this.loading = true;
      const uri = `/categorywithparent?search=''&columns=['id','name']&except=${this.selecteds.toString()}`;
      const response = await fetch(uri);
      const result = await response.json();
      this.multiselectorData = result.items;
      this.loading = false;
    }
  },
  computed: {},

  created() {
    this.form.alternative_products = this.form.alternative_products.map((a) => {
      const { id: value, name: text } = a?.related || {};
      return { value, text };
    });
    this.form.p_group = this.form.p_group.map(({ id, name }) => ({
            value: id,
            text: name,
          }));
    for (var index in this.form.p_group) {
      this.selecteds.push(this.form.p_group[index].value);
    }
  },
};
</script>

<style scoped>
.image-picker {
  margin-bottom: 1rem;
  object-fit: cover;
  border-radius: 100%;
  min-width: 60px;
  min-height: 60px;
  max-width: 60px;
  max-height: 60px;
}

.resource-selector {
  min-width: 20rem;
}.sub-category-selector {
  min-width: 20rem;
}.view-images {
  display: flex;
  margin-top: 20px;
  border-radius: 5px;
  padding: 5px 10px;
  color: #fff;
}
.product-image {
  width: var(--size, 120px);
  height: var(--size, 120px);
  min-width: var(--size, 120px);
  max-width: var(--size, 120px);
  min-height: var(--size, 120px);
  max-height: var(--size, 120px);
  border-radius: 100%;
  overflow: hidden;
  border: 1px solid #d2d6dc;
  background-color: #fafafa;
}
ol {
  min-height: 100px;
  max-height: 500px;
  overflow: auto;
}
ol li {
  display: flex;
  justify-content: space-between;
  padding-bottom: 10px;
  padding-top: 20px;
  border-bottom: 1px solid #d3d3d3;
  align-items: center;
}
.image-picker {
  width: var(--size, 126px);
  height: var(--size, 126px);
  min-width: var(--size, 126px);
  max-width: var(--size, 126px);
  min-height: var(--size, 126px);
  max-height: var(--size, 126px);
  border-radius: 100%;
  overflow: hidden;
  border: 1px solid #d2d6dc;
  background-color: #fafafa;
  position: relative;
}
.image-picker > label {
  height: 100%;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-picker > label > img {
  height: 100%;
  width: 100%;
  object-fit: cover;
}

.image-picker > input {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  opacity: 0;
  cursor: pointer;
}

.image-picker > label i {
  font-size: calc(var(--size, 126px) / 3);
}
jet-button {
  cursor: pointer;
}

.pointer {
cursor: pointer;
}
.selected-options {
display: flex;
flex-flow: column nowrap;
align-items: stretch;
justify-content: flex-start;
}

.selected-options .selected {
display: flex;
flex-flow: row nowrap;
justify-content: flex-start;
align-items: center;
margin-bottom: 0.3rem;
}

.selected-options .selected span {
height: 3rem !important;
border: 1px solid #d2d6dc;
border-radius: 4px;
background-color: white;
flex: 1 0;
display: flex;
flex-flow: row nowrap;
align-items: center;
padding: 0 1rem;
}

.selected-options .selected button {
margin: 0 !important;
margin-left: 0.3rem !important;
}
.multiselect{
min-width: 20rem;
}
</style>
