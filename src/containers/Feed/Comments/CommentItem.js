import React from 'react';
import { StyleSheet, View } from 'react-native';
import {
  Card, Text, Avatar, IconButton,
} from 'components';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import { getRandomUser } from 'mocks/users';

const styles = StyleSheet.create({
  container: {
    marginBottom: scale(14),
    padding: scale(14),
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  name: {
    marginHorizontal: scale(14),
    flex: 1,
  },
  icon: {
    alignSelf: 'flex-start',
  },
  content: {
    marginTop: scale(14),
  },
});

const CommentItem = ({ date, content }) => {
  const user = getRandomUser();
  return (
    <Card style={styles.container}>
      <View style={styles.header}>
        <Avatar source={user.image} size={40} />
        <View style={styles.name}>
          <Text>{user.name}</Text>
          <Text>{date}</Text>
        </View>
        <View style={styles.icon}>
          <IconButton
            icon="more-horizontal"
            size={scale(16)}
            color="gray75"
          />
        </View>
      </View>
      <Text style={styles.content}>{content}</Text>
    </Card>
  );
};

CommentItem.propTypes = {
  content: PropTypes.string.isRequired,
  date: PropTypes.string.isRequired,
};

export default CommentItem;
