/**
 * @author Nicolas CARPi <nico-git@deltablot.email>
 * @copyright 2012 Nicolas CARPi
 * @see https://www.elabftw.net Official website
 * @license AGPL-3.0
 * @package elabftw
 */
import $ from 'jquery';
import 'jquery-ui/ui/widgets/autocomplete';
import Tag from './Tag.class';
import { notif } from './misc';
import i18next from 'i18next';

$(document).ready(function() {
  let type = $('#info').data('type');
  if (type === undefined) {
    type = 'experiments_templates';
  }

  const TagC = new Tag(type);

  // CREATE TAG
  $(document).on('keypress blur', '.createTagInput', function(e) {
    if ($(this).val() === '') {
      return;
    }
    // Enter is ascii code 13
    if (e.which === 13 || e.type === 'focusout') {
      TagC.save($(this).val() as string, $(this).data('id'));
      $(this).val('');
    }
  });

  // AUTOCOMPLETE
  const cache = {};
  ($('.createTagInput') as any).autocomplete({
    source: function(request: any, response: any) {
      const term  = request.term;
      if (term in cache) {
        response(cache[term]);
        return;
      }
      $.getJSON('app/controllers/TagsController.php', request, function(data) {
        cache[term] = data;
        response(data);
      });
    }
  });

  // UNREFERENCE (remove link between tag and entity)
  $(document).on('click', '.tagUnreference', function() {
    TagC.unreference($(this).data('tagid'), $(this).data('id'));
  });

  // DEDUPLICATE (from admin panel/tag manager)
  $(document).on('click', '.tagDeduplicate', function() {
    TagC.deduplicate($(this).data('tag'));
  });

  // DESTROY (from admin panel/tag manager)
  $(document).on('click', '.tagDestroy', function() {
    TagC.destroy($(this).data('tagid'));

  });
});
