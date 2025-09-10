<template>
    <div>
        <Head :title="__(title)" />
        
        <!-- Notificaciones -->
        <div v-if="notification" 
             :class="[
                 'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300',
                 notificationType === 'success' ? 'bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200' : '',
                 notificationType === 'error' ? 'bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200' : '',
                 notificationType === 'info' ? 'bg-blue-100 dark:bg-blue-900 border border-blue-400 dark:border-blue-600 text-blue-700 dark:text-blue-200' : ''
             ]"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg v-if="notificationType === 'success'" class="h-5 w-5 text-green-400 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <svg v-if="notificationType === 'error'" class="h-5 w-5 text-red-400 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <svg v-if="notificationType === 'info'" class="h-5 w-5 text-blue-400 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ notification }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="closeNotification" class="inline-flex text-gray-400 dark:text-gray-300 hover:text-gray-600 dark:hover:text-gray-100">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-md shadow overflow-hidden max-w-full">
            <!-- Zona de drag and drop para subir archivos -->
            <div class="p-6 border-b border-gray-200 bg-white dark:border-gray-700">
                <div
                    class="drag-drop-zone bg-white dark:bg-gray-900"
                    :class="{ 'drag-over': isDragOver, 'uploading': isUploading }"
                    @drop="handleDrop"
                    @dragover.prevent="handleDragOver"
                    @dragenter.prevent="handleDragEnter"
                    @dragleave.prevent="handleDragLeave"
                    @click="triggerFileInput"
                >
                    <input 
                        type="file" 
                        ref="fileInput"
                        multiple
                        @change="handleFileSelect"
                        class="hidden"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar"
                    >
                    
                    <div class="text-center">
                        <svg v-if="!isUploading" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        
                        <div v-if="isUploading" class="mx-auto h-12 w-12 text-indigo-600 dark:text-indigo-400">
                            <svg class="animate-spin h-12 w-12" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span v-if="!isUploading" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 cursor-pointer">
                                    {{ __('Click to upload') }}
                                </span>
                                <span v-if="!isUploading"> {{ __('or drag and drop') }}</span>
                                <span v-if="isUploading" class="font-medium text-indigo-600 dark:text-indigo-400">
                                    {{ __('Uploading files...') }}
                                </span>
                            </p>
                            <p v-if="!isUploading" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('PDF, DOC, XLS, PPT, TXT, JPG, PNG, ZIP up to 10MB') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de archivos seleccionados -->
                <div v-if="selectedFiles.length > 0" class="mt-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Selected files') }}:</h4>
                    <div class="space-y-2">
                        <div v-for="(file, index) in selectedFiles" :key="index" 
                             class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm text-gray-700 dark:text-gray-200">{{ file.name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ formatFileSize(file.size) }})</span>
                            </div>
                            <button @click="removeFile(index)" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex space-x-2">
                        <button 
                            @click="uploadFiles"
                            :disabled="isUploading"
                            class="px-4 py-2 bg-indigo-600 dark:bg-indigo-700 text-white text-sm font-medium rounded hover:bg-indigo-700 dark:hover:bg-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ isUploading ? __('Uploading...') : __('Upload Files') }}
                        </button>
                        <button 
                            @click="clearFiles"
                            :disabled="isUploading"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded hover:bg-gray-400 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ __('Clear') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-8 -mr-6 mb-8 flex flex-wrap">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">{{ __('Ultimas Evidencias') }}</h2>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mb-5">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Name') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Ver') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="evidencia in evidencias.data" :key="evidencia.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ evidencia.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <a :href="evidencia.path" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                    Ver <svg class="inline h-4 w-4 ml-1 text-indigo-500 dark:text-indigo-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
    export default {
        props: {
            evidencias: Object,
            title: String
        },
        layout: Layout,
        metaInfo: { title: 'Vault de Evidencias' },
        data() {
            return {
                isDragOver: false,
                isUploading: false,
                selectedFiles: [],
                maxFileSize: 20 * 1024 * 1024, // 20MB
                allowedTypes: [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain',
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                    'image/gif',
                    'application/zip',
                    'application/x-rar-compressed'
                ],
                notification: null,
                notificationType: 'success' // 'success', 'error', 'info'
            }
        },
        methods: {
            // Sistema de notificaciones
            showNotification(message, type = 'success') {
                this.notification = message
                this.notificationType = type
                
                // Auto-ocultar después de 5 segundos
                setTimeout(() => {
                    this.closeNotification()
                }, 5000)
            },
            
            closeNotification() {
                this.notification = null
            },
            
            // Manejo de eventos de drag and drop
            handleDragEnter(e) {
                e.preventDefault()
                this.isDragOver = true
            },
            
            handleDragOver(e) {
                e.preventDefault()
                this.isDragOver = true
            },
            
            handleDragLeave(e) {
                e.preventDefault()
                // Solo cambiar el estado si realmente salimos del área
                if (!e.currentTarget.contains(e.relatedTarget)) {
                    this.isDragOver = false
                }
            },
            
            handleDrop(e) {
                e.preventDefault()
                this.isDragOver = false
                
                const files = Array.from(e.dataTransfer.files)
                this.processFiles(files)
            },
            
            // Manejo de selección de archivos via click
            triggerFileInput() {
                if (!this.isUploading) {
                    this.$refs.fileInput.click()
                }
            },
            
            handleFileSelect(e) {
                const files = Array.from(e.target.files)
                this.processFiles(files)
                // Limpiar el input para permitir seleccionar el mismo archivo nuevamente
                e.target.value = ''
            },
            
            // Procesamiento y validación de archivos
            processFiles(files) {
                const validFiles = []
                
                files.forEach(file => {
                    // Validar tipo de archivo
                    if (!this.allowedTypes.includes(file.type)) {
                        this.showNotification(`${file.name}: ${this.__('File type not allowed')}`, 'error')
                        return
                    }
                    
                    // Validar tamaño
                    if (file.size > this.maxFileSize) {
                        this.showNotification(`${file.name}: ${this.__('File too large. Maximum size is 10MB')}`, 'error')
                        return
                    }
                    
                    // Verificar que no esté duplicado
                    const isDuplicate = this.selectedFiles.some(existingFile => 
                        existingFile.name === file.name && existingFile.size === file.size
                    )
                    
                    if (!isDuplicate) {
                        validFiles.push(file)
                    }
                })
                
                this.selectedFiles.push(...validFiles)
                
                if (validFiles.length > 0) {
                    this.showNotification(`${validFiles.length} ${this.__('files selected')}`, 'success')
                }
            },
            
            // Gestión de archivos seleccionados
            removeFile(index) {
                this.selectedFiles.splice(index, 1)
            },
            
            clearFiles() {
                this.selectedFiles = []
            },
            
            // Formateo de tamaño de archivo
            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes'
                
                const k = 1024
                const sizes = ['Bytes', 'KB', 'MB', 'GB']
                const i = Math.floor(Math.log(bytes) / Math.log(k))
                
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
            },
            
            // Subida de archivos
            async uploadFiles() {
                if (this.selectedFiles.length === 0) return
                
                this.isUploading = true
                
                try {
                    const formData = new FormData()
                    
                    // Agregar cada archivo al FormData
                    this.selectedFiles.forEach((file, index) => {
                        formData.append(`files[${index}]`, file)
                    })
                    
                    // Realizar la petición usando Inertia
                    await this.$inertia.post(route('evidencias.store'), formData, {
                        forceFormData: true,
                        onSuccess: () => {
                            this.showNotification(this.__('Files uploaded successfully'), 'success')
                            this.clearFiles()
                        },
                        onError: (errors) => {
                            console.error('Upload errors:', errors)
                            this.showNotification(this.__('Error uploading files'), 'error')
                        }
                    })
                    
                } catch (error) {
                    console.error('Upload error:', error)
                    this.showNotification(this.__('Error uploading files'), 'error')
                } finally {
                    this.isUploading = false
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
.drag-drop-zone {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;    
    
    &:hover {
        border-color: #6366f1;
        // background-color: #f8faff;

    }
    
    &.drag-over {
        border-color: #6366f1;
        // background-color: #eef2ff;
        transform: scale(1.02);
        

    }
    
    &.uploading {
        border-color: #6366f1;
        // background-color: #f0f9ff;
        cursor: not-allowed;
        
        @media (prefers-color-scheme: dark) {
            border-color: #818cf8;
            background-color: #1e3a8a;
        }
    }
}

// // Clases dark: de Tailwind también funcionarán
// :global(.dark) .drag-drop-zone {
//     border-color: #4b5563;
//     background-color: #374151;
    
//     &:hover {
//         border-color: #818cf8;
//         background-color: #1e1b4b;
//     }
    
//     &.drag-over {
//         border-color: #818cf8;
//         background-color: #312e81;
//     }
    
//     &.uploading {
//         border-color: #818cf8;
//         background-color: #1e3a8a;
//     }
// }

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>