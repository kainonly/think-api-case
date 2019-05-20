import {NgModule} from '@angular/core';
import {TabsComponent} from './tabs.component';
import {RouterModule, Routes} from '@angular/router';
import {AppExtModule} from '../../app.ext.module';

const routes: Routes = [
  {
    path: '',
    component: TabsComponent,
    children: [
      {
        path: '',
        redirectTo: 'service',
        pathMatch: 'full'
      },
      {
        path: 'service',
        loadChildren: '../tab-service/tab-service.module#TabServiceModule'
      },
      {
        path: 'scenes',
        loadChildren: '../tab-scenes/tab-scenes.module#TabScenesModule'
      }
    ]
  }
];

@NgModule({
  imports: [
    AppExtModule,
    RouterModule.forChild(routes)
  ],
  declarations: [TabsComponent]
})
export class TabsModule {
}
