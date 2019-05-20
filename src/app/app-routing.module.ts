import {NgModule} from '@angular/core';
import {PreloadAllModules, RouterModule, Routes} from '@angular/router';

const routes: Routes = [
    {path: '', redirectTo: 'home', pathMatch: 'full'},
    {path: 'home', loadChildren: './home/home.module#HomePageModule'},
];

@NgModule({
    exports: [RouterModule],
    imports: [
        RouterModule.forRoot(routes, {preloadingStrategy: PreloadAllModules})
    ]
})
export class AppRoutingModule {
}
