@extends('layout.app')

@section('title', 'registrasi')

@section('content')
<div class="row">

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-4">Register User</h3>
                </div>
                <div class="card-body">

                    <div id="register-app">
                        <form @submit.prevent="confirmRegister">
                            @csrf
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="switchPilihKaryawan" v-model="showInputKaryawan">
                                <label class="form-check-label" for="switchPilihKaryawan">
                                    @{{ showInputKaryawan ? 'Gunakan Data Karyawan' : 'Jangan Gunakan Data karyawan' }}
                                </label>
                            </div>

                            <!-- Input Tambahan -->
                            <div class="mb-3" v-if="showInputKaryawan">
                                <label for="input_karyawan" class="form-label">Pilih Karyawan</label>

                                <select 
                                    name="kd_karyawan" 
                                    id="kd_karyawan" 
                                    class="form-control"
                                    v-model="kdKaryawan"
                                >
                                    <option disabled value="">-- Pilih Karyawan --</option>
                                    <option v-for="kry in karyawan" :key="kry.kd_karyawan" :value="kry.kd_karyawan">
                                        @{{ kry.nama_karyawan }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="nama_user" class="form-label">Username</label>
                                <input
                                    type="text"
                                    id="nama_user"
                                    v-model="username"
                                    @input="username = username.toUpperCase()"
                                    class="form-control"
                                    placeholder="Masukkan Username"
                                    autocomplete="off"
                                    autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="level_user" class="form-label">Level User</label>
                                <select
                                    id="id_usr_level"
                                    v-model="selectedLevel"
                                    class="form-control"
                                    name="id_usr_level">
                                    <option disabled value="">-- Pilih Level --</option>
                                    <option v-for="level in levels" :key="level.id_level_user" :value="level.id">
                                        @{{ level.level_user }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input
                                    type="text"
                                    id="password"
                                    v-model="password"
                                    class="form-control"
                                    placeholder="Masukkan Password"
                                    autocomplete="off">
                            </div>

                            <button type="submit" class="btn btn-primary">Buat Akun</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/script/register.js') }}"></script>
@endsection