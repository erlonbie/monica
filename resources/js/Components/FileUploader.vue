<template>
  <div @click="triggerInput">
    <slot />
    <input
      ref="fileInput"
      type="file"
      class="hidden"
      :accept="computedAccept"
      @change="handleFileChange"
      @click.stop
    />
  </div>
</template>

<script>
export default {
  props: {
    inputAcceptTypes: {
      type: String,
      default: null,
    },
    imagesOnly: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['success', 'error'],

  computed: {
    computedAccept() {
      if (this.imagesOnly) {
        return 'image/*';
      }
      return this.inputAcceptTypes;
    },
  },

  methods: {
    triggerInput() {
      this.$refs.fileInput.click();
    },

    handleFileChange(e) {
      const file = e.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append('file', file);

      axios
        .post(route('upload.store'), formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })
        .then((response) => {
          this.$emit('success', response.data);
        })
        .catch((error) => {
          this.$emit('error', error);
        });

      // Reset input
      this.$refs.fileInput.value = null;
    },
  },
};
</script>
