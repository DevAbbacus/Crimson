<template>
  <div class="image-picker" :style="cssvars">
    <label :for="id">
      <img v-if="src" :src="src" />
      <i v-else class="fas fa-image"></i>
    </label>
    <input :id="id" type="file" ref="input" hidden @change="updateImage()" />
  </div>
</template>
<script>
export default {
  props: ['value', 'size'],
  data() {
    const rand = `${Math.round(Math.random() * Date.now())}`;
    return {
      id: rand.slice(rand.length - 4, rand.length),
      src: this.value,
      cssvars: {
        '--size': this.size,
      },
    };
  },
  methods: {
    async updateImage() {
      const file = (this.$refs.input.files || [])[0];
      const fr = new FileReader();
      fr.onload = (e) => {
        this.src = e.srcElement.result;
        this.$emit('input', file);
      };
      if (file) fr.readAsDataURL(file);
    },
  },
};
</script>
<style scoped>
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

.image-picker > label i {
  font-size: calc(var(--size, 126px) / 3);
}
</style>