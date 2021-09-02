<template>
  <div class="sub-category-selector">
    <div class="selected-options">
      <div
        class="selected"
        v-for="selection of selecteds"
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
      v-model="current"
      :openDirection="direction"
      track-by="value"
      label="text"
      :placeholder="placeholder"
      :options="items"
      :searchable="true"
      :hide-selected="true"
      :loading="loading"
      :internal-search="false"
      :options-limit="50"
      :max-height="400"
      :show-no-results="false"
      @click.native="load(search)"
      @search-change="load"
      @input="select"
    >
    </multiselect>
  </div>
</template>
<script>
export default {
  props: ['value', 'placeholder', 'direction', 'loader'],
  data() {
    return {
      loading: false,
      current: null,
      items: [],
      search: '',
      selecteds: this.value || [],
    };
  },
  methods: {
    // this.$emit('input', file);
    async load(search) {
      this.search = search;
      // FIXME: Prevent reload if not change search
      this.loading = true;
      const results = (await this.loader(search)) || [];
      this.items = results.filter(
        (o) => !this.selecteds.find((s) => s.value === o.value)
      );
      this.loading = false;
    },
    select(value) {
      this.search = '';
      this.selecteds.push(value);
      this.current = null;
      this.items = [];
      this.$emit('input', this.selecteds);
    },
    remove(selection) {
      this.selecteds = this.selecteds.filter(
        (i) => i.value !== selection.value
      );
      this.$emit('input', this.selecteds);
    },
  },
};
</script>
<style scoped>
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
</style>