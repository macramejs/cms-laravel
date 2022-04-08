use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Contracts\Filesystem\Filesystem;
    * Create a new file model from the uploaded file.
    *
    * @param UploadedFile $file
    * @param array $attributes
    * @return static
    */
    public static function createFromUploadedFile(UploadedFile $file, array $attributes = []): static
        return DB::transaction(function () use ($file, $attributes) {
            $model = static::create(array_merge([
                'filepath' => Str::uuid(),
                'filename' => $file->getClientOriginalName(),
                'mimetype' => $file->getClientMimeType(),
                'size'     => $file->getSize(),
            ], $attributes));

            $model->setFilepath($model->getKey());

            $model->storage()->putFileAs(
                $model->getFilepath(),
                $file,
                $model->filename
            );

            if ($model->isDirty('filepath')) {
                $model->save();
            return $model;
        });
    /**
     * Gets the url to the file.
     *
     * @return string
     */
     * Gets hhe filesystem for the model.
    /**
     * Get the disk name of the file model.
     *
     * @return void
     */
    /**
     * Set the file path of the model.
     *
     * @return string|null
     */
    /**
     * Get the file path of the model.
     *
     * @return string|null
     */

    /**
     * Get the human readable file size.
     *
     * @return string
     */
    public function getReadableSize()
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $this->size > 1024; $i++) {
            $this->size /= 1024;
        }

        return round($this->size, 2) . ' ' . $units[$i];
    }