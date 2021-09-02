<template>
  <form class="resource-form">
    <image-picker v-model="form.image" />
    <jet-input v-model="form.name" placeholder="Name" />
    <jet-input v-model="form.package_type" placeholder="Package type" />
    <jet-input v-model="form.price" type="number" placeholder="Price" />
    <jet-input v-model="form.discount" type="number" placeholder="Discount" />
    <textarea
      rows="10"
      class="form-input rounded-md shadow-sm"
      placeholder="Description"
      v-model="form.description"
    ></textarea>
    <resource-selector
      :loader="loadProducts"
      v-model="form.products"
      direction="top"
      placeholder="Products"
    />
  </form>
</template>

<script>
import ImagePicker from '../../Shared/ImagePicker';
import JetInput from '../../Jetstream/Input';
import TextInput from '../../Shared/TextInput';
import ResourceSelector from '../../Shared/ResourceSelector';

export default {
  components: {
    ImagePicker,
    JetInput,
    TextInput,
    ResourceSelector,
  },
  props: ['value'],
  data() {
    return {
      form: this.value,
    };
  },
  methods: {
    async loadProducts(search) {
      const uri = `/productslist?search=${search}&columns=['id','name']`;
      const response = await fetch(uri);
      const result = await response.json();
      const products = result.items;
      return products.map(({ id, name }) => ({ value: id, text: name }));
    },
  },
  watch: {
    form: {
      deep: true,
      handler: function () {
        this.$emit('input', this.form);
      },
    },
  },
};
</script>
<style scoped>
.resource-form {
  display: flex;
  flex-flow: column;
  align-items: center;
  justify-content: flex-start;
  max-height: 70vh;
  overflow-y: auto;
}

.image-picker {
  margin-bottom: 1rem;
}

.resource-selector,
input,
textarea {
  width: 80%;
  margin-bottom: 1rem;
}

textarea {
  min-height: 100px;
}
</style>