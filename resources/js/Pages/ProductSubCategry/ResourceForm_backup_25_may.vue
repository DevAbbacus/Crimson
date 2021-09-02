<template>
  <form class="resource-form">
    <jet-input v-model="form.name" placeholder="Name" />
    <textarea
      rows="10"
      class="form-input rounded-md shadow-sm"
      placeholder="Description"
      v-model="form.description"
    ></textarea>
    <category-selector
      :loader="loadProducts"
      v-model="form.p_group"
      direction="top"
      placeholder="Category"
    /><multiselect
  v-model="form.parent_id"
  :options="options"
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
>
  </multiselect>


  </form>
</template>

<script>
import JetInput from '../../Jetstream/Input';
import TextInput from '../../Shared/TextInput';
import CategorySelector from '../../Shared/CategorySelector';
export default {
  components: {
    TextInput,
    JetInput,
    CategorySelector,
  },
  props: ['value'],
  data() {
    return {
      form: this.value,
      options: []
    };
  },
  methods: {
    async loadProducts(search) {
      const uri = `/categorylist?search=${search}&columns=['id','name']`;
      const response = await fetch(uri);
      const result = await response.json();
      const p_group = result.items;
      console.log(p_group);
      return p_group.map(({ id, name }) => ({ value: id, text: name }));
    },
    async loadSubCatories(search) {
      const uri = `/subcategorylist?search=${search}&columns=['id','name']`;
      const response = await fetch(uri);
      const result = await response.json();
      const parent_id = result.items;
      return parent_id.map(({ id, name }) => ({ value: id, text: name }));
    },
    customLabel (option) {
      return `${option.library} - ${option.language}`
    },
    onSelect (option, id) {
      console.log(option, id)
    }
  },
  watch: {
    form: {
      deep: true,
      handler: function () {
        this.$emit('input', this.form);
      },
    },
  },
  async created(){
      const uri = `/categorywithparent?search=''&columns=['id','name']&except=${this.form.parent_id}`;
      const response = await fetch(uri);
      const result = await response.json();
      this.subcategories = result;
      this.options = result.items;
      if(this.form.parent_id != null) {
        const response = await fetch(`/category/${this.form.parent_id}`);
        const result = await response.json();
        this.form.parent_id =result;
      }
  }
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

.category-selector,
input,
textarea {
  width: 80%;
  margin-bottom: 1rem;
}

textarea {
  min-height: 100px;
}
</style>