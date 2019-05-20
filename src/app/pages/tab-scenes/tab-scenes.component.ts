import {Component} from '@angular/core';
import {Router} from '@angular/router';

@Component({
  selector: 'app-tab-scenes',
  templateUrl: './tab-scenes.component.html',
  styleUrls: ['./tab-scenes.component.scss']
})
export class TabScenesComponent {
  constructor(private router: Router) {
  }

  startOauth() {
    this.router.navigateByUrl('https://www.baidu.com',{

    });
  }
}
