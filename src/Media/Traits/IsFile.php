use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Str;
     * Create a new file model from the uploaded file.
     *
     * @param  UploadedFile $file
     * @param  array        $attributes
     * @return static
     */
        return round($this->size, 2).' '.$units[$i];