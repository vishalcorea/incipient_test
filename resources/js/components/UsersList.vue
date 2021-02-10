<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">               
                </div>
                <form class="attach-file" name="user-import" enctype="multipart/form-data" >
                  <input type="file"  id="multiupload" ref="userimport" :name="uploadUser"  @change="filesChange($event.target.files);">
                  <span>{{ error}}</span>
                  <label>Total Skip row</label>  <span>{{skip_count}}</span>
                  <label>Total Import Row </label>  <span>{{ import_row}}</span>
                </form>
            <table >
                <tr>
                  <th>Row Number</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Password</th>
                  <th>Phone Number</th>
                  <th>Gender</th>
                  <th>Address</th>
                  <th>Biography</th>
                  <th>Error</th>
                </tr>
                <tr v-for="(row,index) in skiped_row"  :key="skiped_row.index" >
                    <td>{{index}}</td>             
                    <td>{{row.value[0].first_name}}</td>
                    <td>{{row.value[0].last_name}}</td>
                    <td>{{row.value[0].email}}</td>
                    <td>{{row.value[0].password}}</td>
                    <td>{{row.value[0].phone_number}}</td>
                    <td>{{row.value[0].gender}}</td>
                    <td>{{row.value[0].address}}</td>
                    <td>{{row.value[0].biography}}</td>
                    <td class="err">{{row.email?row.email[0][0]:''}} {{row.phone_number?","+row.phone_number[0][0]:''}}</td>
                </tr>
            </table>
            </div>
        </div>
    </div>
</template>
<style>
.err {
  color: red;
}
</style>
<script>
    export default {
      data() {
      return {
        uploadUser: null,
        uploadError: null,
        currentStatus: null,
        error:'',
        skip_count : '',
        import_row :'',
        skiped_row:[],
          };
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
    filesChange(fileList) {
      if (!fileList.length) return;
      const formData = new FormData();
      let file = fileList[0];
      formData.append("users_file", file);
      axios
      .post("/api/user_import", formData,{headers: {
      'Content-Type': 'multipart/form-data'
      }}).then(response => {
          if(response.data.status == 'fail'){
          this.error = response.data.error;
          }
          if(response.data.status = "success"){
          let total_row ;
          total_row = parseInt(response.data.total_row)-1;
          this.import_row = parseInt(response.data.imported_row);

          this.skip_count = parseInt( total_row - this.import_row);
          this.skiped_row = response.data.data;            
        }          
        console.log(response.data);
      })
      .catch(error => {});
    }
  }
    }
</script>
