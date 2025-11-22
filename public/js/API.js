async function getLevelUser() {
  try {
    const response = await axios.get("/user/level");

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function getDataUserRegister()
{
  try {
    const response = await axios.get("/user/get-user");

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function getAllDataKaryawan() {
  try {
    const response = await axios.get("/hrd/karyawan");

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function checkSession() {
  return await axios.get('/check-session', {
    withCredentials: true
  });
}

async function userByCode(encryptedId) {
  try {
    const response = await axios.get(`/user/detail/${encryptedId}`);

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function getAllDataProvinsi() {
    try {
    const response = await axios.get(`/wilayah/get-provinsi`);

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function getAllDataKotaKabupaten() {
    try {
    const response = await axios.get(`/wilayah/get-kota-kabupaten`);

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function getAllDataKecamatan() {
    try {
    const response = await axios.get(`/wilayah/get-kecamatan`);

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function getAllModule() {
    try {
    const response = await axios.get(`/get-module`);

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function getAllMenu() {
    try {
    const response = await axios.get(`/get-menu`);

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function getAllModuleWithMenu() {
    try {
    const response = await axios.get(`/module-with-menu`);

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

async function getModuleByUser() {
  try {
    const response = await axios.get('/module-by-user', {
      withCredentials: true,
    });

    if (response.data?.status === "success") {
      return response.data.data || [];
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: response.data?.message || "Terjadi kesalahan pada server.",
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan: ${error.response?.data?.message || error.message}`,
    });
    return [];
  }
}

async function getAllDivisi() {
  try {
    const response = await axios.get('/hrd/divisi', {
      withCredentials: true,
    });

    if (response.data?.status === "success") {
      return response.data.data || [];
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: response.data?.message || "Terjadi kesalahan pada API getAllDivisi.",
      });
      return [];
    }
  } catch (error) {
        Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan API getAllDivisi: ${error.response?.data?.message || error.message}`,
    });
    return [];
  }
}

async function getAllPosisition() {
  try {
    const response = await axios.get('/hrd/posisi', {
      withCredentials: true,
    });

    if (response.data?.status === "success") {
      return response.data.data || [];
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: response.data?.message || "Terjadi kesalahan pada API getAllDivisi.",
      });
      return [];
    }
  } catch (error) {
        Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan API getAllDivisi: ${error.response?.data?.message || error.message}`,
    });
    return [];
  }
}

async function getAllDepartement() {
  try {
    const response = await axios.get('/hrd/departement', {
      withCredentials: true,
    });

    if (response.data?.status === "success") {
      return response.data.data || [];
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: response.data?.message || "Terjadi kesalahan pada API getAllDivisi.",
      });
      return [];
    }
  } catch (error) {
        Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan API getAllDivisi: ${error.response?.data?.message || error.message}`,
    });
    return [];
  }
}

async function validasiUserHalaman(data) {
    try {
    const response = await axios.post(`/user/cek-halaman-by-user`, data);

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Akses Ditolak",
        text:
            result.message ||
            "Kamu tidak memiliki akses ke halaman ini.",
        confirmButtonText: "Kembali",
        customClass: {
            confirmButton: "btn btn-danger",
        },
      }).then(() => {
          window.history.back();
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan: ${
            error.response?.data?.message || error.message
        }`,
        confirmButtonText: "Tutup",
        customClass: {
            confirmButton: "btn btn-danger",
        },
        buttonsStyling: false,
    });
    return [];
  }
}

window.getLevelUser = getLevelUser;
window.getDataUserRegister = getDataUserRegister;
window.getAllDataKaryawan = getAllDataKaryawan;
window.checkSession = checkSession;
window.userByCode = userByCode;
window.getAllDataProvinsi = getAllDataProvinsi;
window.getAllDataKotaKabupaten = getAllDataKotaKabupaten;
window.getAllDataKecamatan = getAllDataKecamatan;
window.getAllModule = getAllModule;
window.getAllMenu = getAllMenu;
window.getAllModuleWithMenu = getAllModuleWithMenu;
window.getModuleByUser = getModuleByUser;
window.getAllDivisi = getAllDivisi;
window.getAllDepartement = getAllDepartement;
window.getAllPosisition = getAllPosisition;
window.validasiUserHalaman = validasiUserHalaman;