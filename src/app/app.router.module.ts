import {NgModule} from '@angular/core';
import {PreloadAllModules, RouterModule, Routes} from '@angular/router';

const routes: Routes = [
    {path: '', loadChildren: './home/home.module#HomeModule'},
    {path: 'tabs', loadChildren: './tabs/tabs.module#TabsModule'},
];

@NgModule({
    exports: [RouterModule],
    imports: [
        RouterModule.forRoot(routes, {preloadingStrategy: PreloadAllModules})
    ]
})
export class AppRouterModule {
}
