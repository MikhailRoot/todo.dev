(function(window,angular,undefined){
    'use strict';

    angular.module('AdminApp')
        .component('adminUi',
            {
                bindings:{},
                templateUrl:'/components/adminUiComponent.html',
                controller:['$scope','Todos','FileUploader','Notification',AdminUiComponentController],
                controllerAs:'vm'
            }
        );

    function AdminUiComponentController($scope,Todos,FileUploader,Notification){

        var vm=this;

        vm.$onInit=function AdminUiComponentInit(){
            vm.todos=[];
            vm.newTodo={};
            vm.editingIndex=null;
            vm.pagination={
                limit:3,
                currentPage:1,
                maxItemsCount:1
            };

            vm.imageuploader = new FileUploader({
                url: '/todo/photo',
                autoUpload: true,
                method:'POST',
                formData:[],
                filters:[
                    {
                        name: 'imageFilter',
                        fn: function(item /*{File|FileLikeObject}*/, options) {
                            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
                            return '|jpg|png|jpeg|bmp|'.indexOf(type) !== -1;
                        }
                    }
                ]
            });

            vm.imageuploader.onSuccessItem = function (fileItem, response, status, headers) {
                vm.newTodo.photo=response.filename;

                console.info('onSuccessItem', fileItem, response, status, headers);

            };
            vm.imageuploader.onErrorItem = function (fileItem, response, status, headers) {
                console.info('onErrorItem', fileItem, response, status, headers);
            };

            Todos.getList().then(function(result){
                vm.todos=result;
               updatePagination();
            }).catch(errorHandler);

        };

        vm.selectNewPage=function selectNewPage(){
            var page=vm.pagination.currentPage;
            Todos.getPageByNumber(page).then(function(result){
                vm.todos=result;
                vm.pagination=Todos.getCurrentPaginationOptions();
                updatePagination();
            }).catch(errorHandler);

        };

        function updatePagination(){
            var pagination=Todos.getCurrentPaginationOptions();
            vm.pagination.maxItemsCount=pagination.maxItemsCount;
            vm.pagination.limit=pagination.limit;
            vm.pagination.currentPage=pagination.currentPage;
            vm.pagination.orderBy=pagination.orderBy;
            // lets refresh newTodo and editor too
            vm.newTodo={};
            vm.editingIndex=null;
        }

        function errorHandler(err){
            Notification.error({'title':'Error','message':'Sorry network error occured'});
            //alert('Sorry network error');
        }

        vm.createTodo=function createTodo(){
            if(vm.newTodo.editing===true){
                Todos.update(vm.newTodo).then(function(todo){
                    vm.todos[vm.editingIndex]=todo;
                    vm.newTodo={};
                    Notification.success({'title':'Updated','message':'Todo updated'});
                }).catch(errorHandler);
            }else{
                Todos.create(vm.newTodo).then(function(todo){
                    removeTodoPreviews();
                    vm.todos.unshift(todo);
                    vm.newTodo={};
                    Notification.success({'title':'Created','message':'Todo created'});
                }).catch(errorHandler)
            }
        };

        function removeTodoPreviews(){
            vm.todos=vm.todos.filter(function(obj){
                return obj.preview === undefined;
            });
        }

        vm.previewNewTodo=function previewNewTodo(){
            vm.newTodo.preview=true;
            vm.todos.unshift(vm.newTodo);
        };

        vm.cancel=function cancel(){

            if(vm.newTodo.id){
                vm.todos[vm.editingIndex].editing=false;
                vm.editingIndex=null;
                vm.newTodo={};
            }else{
                vm.newTodo.preview=false;
                removeTodoPreviews();
            }

        };

        vm.editTodo= function editTodo(todo,index){
            vm.editingIndex=index;
            vm.newTodo.editing=false;
            vm.newTodo=todo;
            vm.newTodo.editing=true;
        };

        vm.removePhoto=function(){

            if(vm.newTodo.editing===true){
                Todos.removePhoto(vm.newTodo).then(function(result){
                    vm.newTodo=result;
                }).catch(errorHandler);
            }else{
                vm.newTodo.photo='';
            }

        };

        vm.deleteTodo=function deleteTodo(){
            Todos.deleteItem(vm.newTodo).then(function(){
                vm.todos.splice(vm.editingIndex,1);
                vm.newTodo={};
                Notification.success({'title':'Deleted','message':'Todo deleted successfully'});
            }).catch(errorHandler);

        };

        vm.updateSorting=function updateSorting(fieldName){

            if(!vm.pagination.orderBy[fieldName]){
                vm.pagination.orderBy[fieldName]='ASC';
            }else if(vm.pagination.orderBy[fieldName]==='ASC'){
                vm.pagination.orderBy[fieldName]='DESC';
            }else if(vm.pagination.orderBy[fieldName]==='DESC'){
                if(fieldName==='id' && (!vm.pagination.orderBy['username'] && !vm.pagination.orderBy['email'] && !vm.pagination.orderBy['state'] ) ){
                    vm.pagination.orderBy['id']='ASC';
                }else{
                    vm.pagination.orderBy[fieldName]=undefined;
                }

            }
            // lets check pagination
            if( vm.pagination.orderBy['username'] || vm.pagination.orderBy['email'] || vm.pagination.orderBy['state']){
                vm.pagination.orderBy['id']=null;
            }
            if(!vm.pagination.orderBy['username'] && !vm.pagination.orderBy['email'] && !vm.pagination.orderBy['state'] && !vm.pagination.orderBy['id']){
                vm.pagination.orderBy['id']='DESC';//default fallback
            }
            vm.pagination.offset=0;
            vm.pagination.limit=3;
            vm.pagination.currentPage=1;

            Todos.getList(vm.pagination).then(function(result){
                vm.todos=result;
                updatePagination();
            }).catch(errorHandler);


        }

    }

})(window,angular);