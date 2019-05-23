import {Component} from '@angular/core';
import {Router} from '@angular/router';

@Component({
  selector: 'app-tabs',
  templateUrl: './tabs.component.html',
  styleUrls: ['./tabs.component.scss']
})
export class TabsComponent {
  title = '服务测试';
  selectedIndex = 0;

  constructor(private router: Router) {
  }

  tabBarTabOnPress(pressParam: any) {
    switch (pressParam.index) {
      case 0:
        this.title = '服务测试';
        this.router.navigateByUrl('/tabs/service');
        break;
      case 1:
        this.title = '场景模拟';
        this.router.navigateByUrl('/tabs/scenes');
        break;
    }
    this.selectedIndex = pressParam.index;
  }

  back() {
    this.router.navigateByUrl('/');
  }
}
